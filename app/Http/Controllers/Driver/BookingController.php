<?php

namespace App\Http\Controllers\Driver;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\SystemLogger;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $driver = Auth::guard('driver')->user();
        if (!$driver) {
            return redirect()->route('driver.login');
        }

        // Normalize service IDs to integers and avoid empty whereIn() calls
        // Use the loaded collection to avoid ambiguous SQL when plucking from the relation query
        $serviceIds = $driver->services->pluck('id')->map(function ($id) { return (int)$id; })->filter()->values()->toArray();

        // Include bookings where the driver is assigned as pickup or delivery as well as legacy driver_id
        $bookingsQuery = Booking::with(['user', 'service', 'serviceCategory', 'serviceType', 'lab'])
            ->where(function($q) use ($serviceIds, $driver) {
                $driverId = (int)$driver->id;
                if (!empty($serviceIds)) {
                    $q->whereIn('service_id', $serviceIds)
                        ->orWhere('driver_id', $driverId)
                        ->orWhere('pickup_driver_id', $driverId)
                        ->orWhere('delivery_driver_id', $driverId);
                } else {
                    // If driver has no service assignments, return bookings explicitly assigned to them
                    $q->where(function($sub) use ($driverId) {
                        $sub->where('driver_id', $driverId)
                            ->orWhere('pickup_driver_id', $driverId)
                            ->orWhere('delivery_driver_id', $driverId);
                    });
                }
            })
            ->orderByDesc('created_at');

        if ($status = $request->get('status')) {
            $bookingsQuery->where('status', $status);
        }

        $bookings = $bookingsQuery->paginate(15)->withQueryString();

        return view('driver.bookings.index', compact('bookings', 'driver'));
    }

    public function show(Booking $booking)
    {
        $driver = Auth::guard('driver')->user();
        if (!$driver) {
            return redirect()->route('driver.login');
        }

        try {
            // Normalize service ids and do strict int comparison to avoid type mismatches
            $serviceIds = $driver->services->pluck('id')->map(function($id){ return (int)$id; })->toArray();
            $bookingServiceId = (int)($booking->service_id ?? 0);
            $bookingDriverId = (int)($booking->driver_id ?? 0);
            $isPickup = ((int)($booking->pickup_driver_id ?? 0) === (int)$driver->id);
            $isDelivery = ((int)($booking->delivery_driver_id ?? 0) === (int)$driver->id);

            // Authorize if driver handles booking service or is assigned (legacy driver_id) or is pickup/delivery
            if (!in_array($bookingServiceId, $serviceIds, true)
                && $bookingDriverId !== (int)$driver->id
                && !$isPickup
                && !$isDelivery) {
                abort(403, 'Not authorized.');
            }

            $booking->load(['user', 'service', 'serviceCategory', 'serviceType']);
            $labs = \App\Models\Lab::all();

            // Render view inside try so we can catch rendering errors and log details
            return view('driver.bookings.show', compact('booking', 'driver', 'labs'));
        } catch (\Throwable $e) {
            // Log detailed info to the application log
            Log::error('Driver BookingController@show exception', [
                'message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'driver_id' => $driver->id ?? null,
                'booking_id' => $booking->id ?? null,
                'request_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'request_input' => request()->all(),
            ]);

            // Try to persist an audit log (non-blocking)
            try {
                SystemLogger::record([
                    'actor_type' => 'driver',
                    'actor_id' => $driver->id ?? null,
                    'actor_name' => $driver->name ?? ($driver->email ?? null),
                    'event_type' => 'error',
                    'event_subtype' => 'booking_show_exception',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'payload' => [
                        'exception_message' => $e->getMessage(),
                        'exception_class' => get_class($e),
                        'booking_id' => $booking->id ?? null,
                        'request_input' => request()->all(),
                    ],
                ]);
            } catch (\Throwable $inner) {
                // If logging fails, write to the application log and continue
                Log::error('Failed to record system log for booking_show_exception', ['error' => $inner->getMessage()]);
            }

            if (request()->wantsJson() || request()->ajax() || request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Internal server error'], 500);
            }

            // Redirect back with a friendly message. Admins can inspect logs for details.
            return redirect()->route('driver.bookings.index')->with('error', 'An internal error occurred while loading the booking. The incident has been logged.');
        }
    }

    public function update(Request $request, Booking $booking)
    {
        $driver = Auth::guard('driver')->user();
        if (!$driver) {
            return redirect()->route('driver.login');
        }

        $serviceIds = $driver->services->pluck('id')->toArray();
        if (!in_array($booking->service_id, $serviceIds)) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,pickup,delivered,canceled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        // Record status change in system logs if it actually changed
        if ($oldStatus !== $request->status) {
            try {
                // Ensure related models are loaded so we can include their names/fields in the payload
                $booking = $booking->fresh();
                $booking->load(['service', 'user', 'lab', 'driver']);

                // Build richer payload with booking, service, customer, lab and driver context
                $payload = [
                    'booking_id' => $booking->id,
                    'order_number' => $booking->order_number ?? null,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'service' => [
                        'id' => $booking->service_id ?? null,
                        'name' => data_get($booking, 'service.name') ?? null,
                    ],
                    'customer' => [
                        'id' => data_get($booking, 'user.id') ?? null,
                        'name' => data_get($booking, 'user.name') ?? data_get($booking, 'user.phone') ?? null,
                        'phone' => data_get($booking, 'user.phone') ?? null,
                    ],
                    'lab' => [
                        'id' => $booking->lab_id ?? null,
                        'name' => data_get($booking, 'lab.name') ?? null,
                        'phone' => data_get($booking, 'lab.phone') ?? null,
                    ],
                    'driver' => [
                        'id' => $booking->driver_id ?? $driver->id ?? null,
                        'name' => $driver->name ?? null,
                        'email' => $driver->email ?? null,
                    ],
                    'request_input' => $request->except(['_token', '_method']),
                    'route' => optional(request()->route())->getName() ?? null,
                ];

                // Persist actor info: prefer authenticated driver, otherwise record as System
                $actorType = 'system';
                $actorId = null;
                $actorName = $driver->name;
                if ($driver) {
                    $actorType = 'driver';
                    $actorId = $driver->id;
                    $actorName = $driver->name ?? ($driver->email ?? ($driver->phone ?? null));
                }

                SystemLogger::record([
                    'actor_type' => $actorType,
                    'actor_id' => $actorId,
                    'actor_name' => $actorName,
                    'event_type' => 'booking',
                    'event_subtype' => 'status_changed',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'payload' => $payload,
                ]);
            } catch (\Exception $e) {
                Log::error('SystemLogger failed recording driver status change', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'driver_id' => $driver->id]);
            }
        }

        if ($oldStatus !== $request->status && $booking->user) {
            $notificationClass = match ($request->status) {
                'pending' => \App\Notifications\PendingBookingNotification::class,
                'pickup' => \App\Notifications\PickupBookingNotification::class,
                'delivered' => \App\Notifications\DeliveredBookingNotification::class,
                'canceled' => \App\Notifications\CanceledBookingNotification::class,
                default => null,
            };

            if ($notificationClass) {
                try {
                    $booking->user->notify(new $notificationClass($booking));
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                }
            }
        }

        return redirect()->route('driver.bookings.show', $booking)->with('success', 'Status updated.');
    }

    public function assignLab(Request $request, Booking $booking)
    {
        $driver = Auth::guard('driver')->user();
        if (!$driver) {
            return redirect()->route('driver.login');
        }

        $serviceIds = $driver->services->pluck('id')->toArray();
        $isPickup = ((int)($booking->pickup_driver_id ?? $booking->driver_id ?? 0) === (int)$driver->id);
        $isDelivery = ((int)($booking->delivery_driver_id ?? 0) === (int)$driver->id);
        if (!in_array($booking->service_id, $serviceIds) && !$isPickup && !$isDelivery) {
            abort(403);
        }


        // Do not allow assigning a lab for service id 3
        if (($booking->service_id ?? null) === 3) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Lab assignment is not applicable for this service.'], 422);
            // Notify admins about driver-triggered status change
            try {
                $admins = \App\Models\Admin::query()->get();
                foreach ($admins as $a) {
                    \App\Services\NotificationService::safeNotify($a, new \App\Notifications\AdminDriverActionNotification($booking, $driver->name ?? ($driver->email ?? 'Driver'), 'status_changed'), ['booking_id' => $booking->id]);
                }
            } catch (\Throwable $e) {
                Log::error('Failed to notify admins of driver status change', ['error' => $e->getMessage(), 'booking_id' => $booking->id]);
            }
            }
            return redirect()->back()->with('error', 'Lab assignment is not applicable for this service.');
        }

        $validator = Validator::make($request->all(), [
            'lab_id' => 'required|exists:labs,id',
        ]);

        if ($validator->fails()) {
            // Notify admins about the status update made by driver (non-AJAX flow already notified above)
            try {
                $admins = \App\Models\Admin::query()->get();
                foreach ($admins as $a) {
                    \App\Services\NotificationService::safeNotify($a, new \App\Notifications\AdminDriverActionNotification($booking, $driver->name ?? ($driver->email ?? 'Driver'), 'status_changed'), ['booking_id' => $booking->id]);
                }
            } catch (\Throwable $e) {
                Log::warning('Admin notification after driver update failed (post-redirect)', ['err' => $e->getMessage()]);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $booking->lab_id = $request->lab_id;
        $booking->lab_assigned_at = now();
        $this->moveToPickupIfApplicable($booking);
        $booking->save();

        if ($booking->user) {
            try {
                $booking->user->notify(new \App\Notifications\LabAssignedNotification($booking));
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        // Notify admins about lab assignment by driver
        try {
            $admins = \App\Models\Admin::query()->get();
            foreach ($admins as $a) {
                \App\Services\NotificationService::safeNotify($a, new \App\Notifications\AdminDriverActionNotification($booking, $driver->name ?? ($driver->email ?? 'Driver'), 'status_changed'), ['booking_id' => $booking->id, 'action' => 'lab_assigned']);
            }
        } catch (\Throwable $e) {
            Log::warning('Admin notification for lab assignment failed', ['err' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            $booking->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Assigned to lab.',
                'booking' => [
                    'id' => $booking->id,
                    'lab_id' => $booking->lab_id,
                ],
            ]);
        }

        return redirect()->route('driver.bookings.show', $booking)->with('success', 'Assigned to lab.');
    }

    public function markArrivedAtLab(Request $request, Booking $booking)
    {
        $driver = Auth::guard('driver')->user();
        if (!$driver) {
            return redirect()->route('driver.login');
        }

        $serviceIds = $driver->services->pluck('id')->toArray();
        $isPickup = ((int)($booking->pickup_driver_id ?? $booking->driver_id ?? 0) === (int)$driver->id);
        $isDelivery = ((int)($booking->delivery_driver_id ?? 0) === (int)$driver->id);
        if (!in_array($booking->service_id, $serviceIds) && !$isPickup && !$isDelivery) {
            abort(403);
        }


        // Prevent lab actions for service id 3
        if (($booking->service_id ?? null) === 3) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Lab actions are not applicable for this service.'], 422);
            }
            return redirect()->back()->with('error', 'Lab actions are not applicable for this service.');
        }

        if ($request->has('lab_id')) {
            $request->validate(['lab_id' => 'nullable|exists:labs,id']);
            if (!$booking->lab_id) {
                $booking->lab_id = $request->lab_id;
                $booking->lab_assigned_at = now();
            }
        }

        if (!$booking->lab_id) {
            return redirect()->back()->with('error', 'Assign a lab first.');
        }

        $booking->lab_arrived_at = now();
        $this->moveToPickupIfApplicable($booking);
        $booking->save();

        // Log lab arrived action by driver
        try {
            SystemLogger::record([
                'actor_type' => 'driver',
                'actor_id' => $driver->id,
                'actor_name' => $driver->name ?? ($driver->email ?? ($driver->phone ?? null)),
                'event_type' => 'booking',
                'event_subtype' => 'lab_arrived',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('SystemLogger failed recording driver lab_arrived', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'driver_id' => $driver->id]);
        }
        if ($booking->user) {
            try {
                $booking->user->notify(new \App\Notifications\LabArrivedNotification($booking));
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        // Notify admins about driver arrival at lab
        try {
            $admins = \App\Models\Admin::query()->get();
            foreach ($admins as $a) {
                \App\Services\NotificationService::safeNotify($a, new \App\Notifications\AdminDriverActionNotification($booking, $driver->name ?? ($driver->email ?? 'Driver'), 'arrived_at_lab'), ['booking_id' => $booking->id]);
            }
        } catch (\Throwable $e) {
            Log::warning('Admin notification for driver arrival failed', ['err' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            $booking->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Arrived at lab.',
            ]);
        }

        return redirect()->route('driver.bookings.show', $booking)->with('success', 'Arrived at lab.');
    }

    public function markPickedFromLab(Request $request, Booking $booking)
    {
        $driver = Auth::guard('driver')->user();
        if (!$driver) {
            return redirect()->route('driver.login');
        }

        $serviceIds = $driver->services->pluck('id')->toArray();
        $isPickup = ((int)($booking->pickup_driver_id ?? $booking->driver_id ?? 0) === (int)$driver->id);
        $isDelivery = ((int)($booking->delivery_driver_id ?? 0) === (int)$driver->id);
        if (!in_array($booking->service_id, $serviceIds) && !$isPickup && !$isDelivery) {
            abort(403);
        }


        // Prevent lab actions for service id 3
        if (($booking->service_id ?? null) === 3) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Lab actions are not applicable for this service.'], 422);
            }
            return redirect()->back()->with('error', 'Lab actions are not applicable for this service.');
        }

        if ($request->has('lab_id')) {
            $request->validate(['lab_id' => 'nullable|exists:labs,id']);
            if (!$booking->lab_id) {
                $booking->lab_id = $request->lab_id;
                $booking->lab_assigned_at = now();
            }
        }

        if (!$booking->lab_id) {
            return redirect()->back()->with('error', 'Assign a lab first.');
        }

        $booking->lab_picked_at = now();
        $this->moveToPickupIfApplicable($booking);
        $booking->save();

        // Log lab picked action by driver
        try {
            SystemLogger::record([
                'actor_type' => 'driver',
                'actor_id' => $driver->id,
                'actor_name' => $driver->name ?? ($driver->email ?? ($driver->phone ?? null)),
                'event_type' => 'booking',
                'event_subtype' => 'lab_picked',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('SystemLogger failed recording driver lab_picked', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'driver_id' => $driver->id]);
        }
        // Log lab picked action by driver (for alternate flow)
        try {
            SystemLogger::record([
                'actor_type' => 'driver',
                'actor_id' => $driver->id,
                'actor_name' => $driver->name ?? ($driver->email ?? ($driver->phone ?? null)),
                'event_type' => 'booking',
                'event_subtype' => 'lab_picked',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('SystemLogger failed recording driver lab_picked', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'driver_id' => $driver->id]);
        }
        if ($booking->user) {
            try {
                $booking->user->notify(new \App\Notifications\LabPickedNotification($booking));
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        // Notify admins about driver picking from lab
        try {
            $admins = \App\Models\Admin::query()->get();
            foreach ($admins as $a) {
                \App\Services\NotificationService::safeNotify($a, new \App\Notifications\AdminDriverActionNotification($booking, $driver->name ?? ($driver->email ?? 'Driver'), 'picked_from_lab'), ['booking_id' => $booking->id]);
            }
        } catch (\Throwable $e) {
            Log::warning('Admin notification for driver picked failed', ['err' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            $booking->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Picked from lab.',
            ]);
        }

        return redirect()->route('driver.bookings.show', $booking)->with('success', 'Picked from lab.');
    }

    public function markDriverCollected(Request $request, Booking $booking)
    {
        $driver = Auth::guard('driver')->user();
        if (!$driver) {
            return redirect()->route('driver.login');
        }

        $serviceIds = $driver->services->pluck('id')->toArray();
        $isPickup = ((int)($booking->pickup_driver_id ?? $booking->driver_id ?? 0) === (int)$driver->id);
        $isDelivery = ((int)($booking->delivery_driver_id ?? 0) === (int)$driver->id);
        if (!in_array($booking->service_id, $serviceIds) && !$isPickup && !$isDelivery) {
            abort(403);
        }

        $booking->driver_collected_at = now();
        $this->moveToPickupIfApplicable($booking);
        $booking->save();

        // Log driver collected action
        try {
            SystemLogger::record([
                'actor_type' => 'driver',
                'actor_id' => $driver->id,
                'actor_name' => $driver->name ?? ($driver->email ?? ($driver->phone ?? null)),
                'event_type' => 'booking',
                'event_subtype' => 'driver_collected',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('SystemLogger failed recording driver collected', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'driver_id' => $driver->id]);
        }
        if ($booking->user) {
            try {
                $booking->user->notify(new \App\Notifications\DriverCollectedNotification($booking));
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        // Notify admins about driver collection
        try {
            $admins = \App\Models\Admin::query()->get();
            foreach ($admins as $a) {
                \App\Services\NotificationService::safeNotify($a, new \App\Notifications\AdminDriverActionNotification($booking, $driver->name ?? ($driver->email ?? 'Driver'), 'driver_collected'), ['booking_id' => $booking->id]);
            }
        } catch (\Throwable $e) {
            Log::warning('Admin notification for driver collected failed', ['err' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            $booking->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Collected from user.',
            ]);
        }

        return redirect()->route('driver.bookings.show', $booking)->with('success', 'Collected.');
    }

    public function markReturnedToUser(Request $request, Booking $booking)
    {
        $driver = Auth::guard('driver')->user();
        if (!$driver) {
            return redirect()->route('driver.login');
        }

        $serviceIds = $driver->services->pluck('id')->toArray();
        $isPickup = ((int)($booking->pickup_driver_id ?? $booking->driver_id ?? 0) === (int)$driver->id);
        $isDelivery = ((int)($booking->delivery_driver_id ?? 0) === (int)$driver->id);
        if (!in_array($booking->service_id, $serviceIds) && !$isPickup && !$isDelivery) {
            abort(403);
        }

        $booking->driver_returned_at = now();
        // if status not delivered, set delivered
        if (strtolower($booking->status ?? '') !== 'delivered') {
            $booking->status = 'delivered';
        }
        $booking->save();

        // Log returned action
        try {
            SystemLogger::record([
                'actor_type' => 'driver',
                'actor_id' => $driver->id,
                'actor_name' => $driver->name ?? ($driver->email ?? ($driver->phone ?? null)),
                'event_type' => 'booking',
                'event_subtype' => 'driver_returned',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => [
                    'booking_id' => $booking->id,
                    'status' => $booking->status,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('SystemLogger failed recording driver returned', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'driver_id' => $driver->id]);
        }

        if ($booking->user) {
            try {
                $booking->user->notify(new \App\Notifications\DriverReturnedNotification($booking));
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        // Notify admins about return
        try {
            $admins = \App\Models\Admin::query()->get();
            foreach ($admins as $a) {
                \App\Services\NotificationService::safeNotify($a, new \App\Notifications\AdminDriverActionNotification($booking, $driver->name ?? ($driver->email ?? 'Driver'), 'returned_to_user'), ['booking_id' => $booking->id]);
            }
        } catch (\Throwable $e) {
            Log::warning('Admin notification for driver returned failed', ['err' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            $booking->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Returned to user.',
                'booking' => ['id' => $booking->id, 'driver_returned_at' => $booking->driver_returned_at],
            ]);
        }

        return redirect()->route('driver.bookings.show', $booking)->with('success', 'Returned to user.');
    }

    private function moveToPickupIfApplicable(Booking $booking): void
    {
        $current = strtolower((string) ($booking->status ?? ''));
        if (in_array($current, ['delivered', 'canceled', 'cancelled'], true)) {
            return;
        }

        $booking->status = 'pickup';
    }
}
