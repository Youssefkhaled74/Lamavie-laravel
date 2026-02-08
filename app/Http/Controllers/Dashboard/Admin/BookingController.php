<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Notifications\BookingStatusUpdated;
use App\Services\SystemLogger;
use App\Exports\BookingsExport;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    public function __construct()
    {
        // Ensure admin is authenticated for all actions
        $this->middleware('auth:admin');

        // General booking management permission for listing, viewing and updating
        $this->middleware('permission:manage bookings')->only(['index', 'show', 'update', 'destroy', 'export', 'trashed', 'restore', 'search', 'unseen']);

        // Specific permissions for assignment actions
        $this->middleware('permission:assign lab')->only(['assignLab']);
        $this->middleware('permission:assign driver')->only(['assignDriver']);
        $this->middleware('permission:assign car')->only(['assignCar']);
    }
    public function index(Request $request)
    {
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $bookingsQuery = Booking::with(['user', 'service', 'serviceCategory', 'serviceType', 'paymentMethod'])
            ->orderByDesc('created_at');

        // Apply filters - using loose comparison for better compatibility
        if ($request->filled('service_id')) {
            $bookingsQuery->where('service_id', $request->service_id);
        }
        if ($request->filled('service_category_id')) {
            $bookingsQuery->where('service_category_id', $request->service_category_id);
        }
        if ($request->filled('service_type_id')) {
            $bookingsQuery->where('service_type_id', $request->service_type_id);
        }
        if ($request->filled('status')) {
            $bookingsQuery->where('status', $request->status);
        }
        if ($request->filled('payment_method_id')) {
            $bookingsQuery->where('payment_method_id', $request->payment_method_id);
        }
        if ($request->filled('date_from')) {
            $bookingsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $bookingsQuery->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('date') && !$request->filled('date_from')) {
            $bookingsQuery->whereDate('created_at', $request->date);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $bookingsQuery->whereHas('user', function ($query) use ($q) {
                $query->where('name', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }

        $bookings = $bookingsQuery->paginate(15)->withQueryString();

        // Get filter options
        $services = \App\Models\Service::all();
        $categories = \App\Models\ServiceCategory::all();
        $types = \App\Models\ServiceType::all();
        $paymentMethods = \App\Models\PaymentMethod::all();

        return view('dashboard.admin.bookings.index', compact('bookings', 'admin', 'services', 'categories', 'types', 'paymentMethods'));
    }

    public function show(Booking $booking)
    {
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $booking->update(['is_unseen' => false]);
        $booking->load(['user', 'service', 'serviceCategory', 'serviceType', 'lab', 'driver']);

        // Determine if this booking is a Car Wash so the view can show assign-car UI
        $isCarWash = false;
        if (($booking->service_id ?? null) === 3) {
            $isCarWash = true;
        } elseif ($booking->service && is_array(data_get($booking, 'service.name'))) {
            $name = data_get($booking, 'service.name')[app()->getLocale()] ?? '';
            if (stripos($name, 'car') !== false) $isCarWash = true;
        } elseif ($booking->service && is_string(data_get($booking, 'service.name'))) {
            if (stripos($booking->service->name, 'car') !== false) $isCarWash = true;
        }

        $driverVehicles = [];
        $assignment = null;
        if ($isCarWash) {
            $dvModel = new \App\Models\DriverVehicle();
            $dvTable = $dvModel->getTable();
            $dvQuery = \App\Models\DriverVehicle::query();
            if (Schema::hasColumn($dvTable, 'deleted_at')) {
                $dvQuery->whereNull('deleted_at');
            }
            $driverVehicles = $dvQuery->get();
            // load existing car assignment for this booking if present (with vehicle relation)
            $assignment = \App\Models\BookingCarAssignment::with(['vehicle.drivers'])->where('booking_id', $booking->id)->first();
        } else {
            $assignment = null;
        }

        if ($isCarWash) {
                // Log view event (structured)
                Log::info('Booking viewed', ['booking_id' => $booking->id, 'admin_id' => $admin->id ?? null, 'isCarWash' => true, 'request_id' => request()->header('X-Request-Id') ?? null]);

                return view('dashboard.admin.bookings.show_carwash', compact('booking', 'admin', 'isCarWash', 'driverVehicles', 'assignment'));
        }

        // Non car-wash bookings (e.g. dry-clean) use a dedicated dry-clean view
        // Log view event for non-carwash
        Log::info('Booking viewed', ['booking_id' => $booking->id, 'admin_id' => $admin->id ?? null, 'isCarWash' => false, 'request_id' => request()->header('X-Request-Id') ?? null]);

        return view('dashboard.admin.bookings.show_dryclean', compact('booking', 'admin', 'isCarWash', 'driverVehicles', 'assignment'));
    }

    /**
     * Show edit form for a booking so admin can change any allowed fields.
     */
    public function edit(Booking $booking)
    {
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin) return redirect()->route('admin.login');

        $booking->load(['user', 'service', 'paymentMethod', 'lab', 'driver']);
        // Guard drivers and labs queries in case the tables don't have soft deletes
        $driversQuery = \App\Models\Driver::query();
        $driverModel = new \App\Models\Driver();
        if (Schema::hasColumn($driverModel->getTable(), 'deleted_at')) {
            $driversQuery->whereNull('deleted_at');
        }
        $drivers = $driversQuery->get();

        $labsQuery = \App\Models\Lab::query();
        $labModel = new \App\Models\Lab();
        if (Schema::hasColumn($labModel->getTable(), 'deleted_at')) {
            $labsQuery->whereNull('deleted_at');
        }
        $labs = $labsQuery->get();

        $paymentMethods = \App\Models\PaymentMethod::all();

        // Provide driver vehicles list for car-wash bookings
        $isCarWash = false;
        if (($booking->service_id ?? null) === 3) {
            $isCarWash = true;
        } elseif ($booking->service && is_array(data_get($booking, 'service.name'))) {
            $name = data_get($booking, 'service.name')[app()->getLocale()] ?? '';
            if (stripos($name, 'car') !== false) $isCarWash = true;
        } elseif ($booking->service && is_string(data_get($booking, 'service.name'))) {
            if (stripos($booking->service->name, 'car') !== false) $isCarWash = true;
        }

        $driverVehicles = [];
        $assignment = null;
        if ($isCarWash) {
            $dvModel = new \App\Models\DriverVehicle();
            $dvTable = $dvModel->getTable();
            $dvQuery = \App\Models\DriverVehicle::query();
            if (Schema::hasColumn($dvTable, 'deleted_at')) {
                $dvQuery->whereNull('deleted_at');
            }
            $driverVehicles = $dvQuery->get();
        }

        $statuses = ['pending' => 'Pending', 'pickup' => 'Pickup', 'delivered' => 'Delivered', 'canceled' => 'Canceled'];

        return view('dashboard.admin.bookings.edit', compact('booking', 'admin', 'drivers', 'labs', 'paymentMethods', 'statuses', 'isCarWash', 'driverVehicles', 'assignment'));
    }

    public function update(\App\Http\Requests\BookingUpdateRequest $request, Booking $booking)
    {
        Log::info('Booking update: Start', ['booking_id' => $booking->id]);
        /** @var \App\Models\Admin|null $admin */
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            Log::warning('Booking update: Admin not authenticated', ['booking_id' => $booking->id]);
            return redirect()->route('admin.login');
        }

        Log::info('Booking update: Validation start', ['booking_id' => $booking->id]);

        // Allowed fields for admin updates
        $allowed = [
            'status', 'driver_id', 'pickup_driver_id', 'delivery_driver_id', 'lab_id', 'pickup_date', 'pickup_time', 'pickup_location', 'delivery_location',
            'total', 'payment_method_id', 'notes'
        ];

        $input = $request->only($allowed);

        // Validation rules
        $rules = [
            'status' => 'nullable|in:pending,pickup,delivered,canceled',
            'driver_id' => 'nullable|exists:drivers,id',
            'pickup_driver_id' => 'nullable|exists:drivers,id',
            'delivery_driver_id' => 'nullable|exists:drivers,id',
            'lab_id' => 'nullable|exists:labs,id',
            'pickup_date' => 'nullable|date',
            'pickup_time' => 'nullable|string',
            'pickup_location' => 'nullable|string',
            'delivery_location' => 'nullable|string',
            // decimal(10,2) max is 99,999,999.99 - prevent out-of-range values being written
            'total' => 'nullable|numeric|between:0,99999999.99',
            // `payment_methods` table may not exist in some deployments (lightweight installs).
            // Only apply the `exists` rule if the table is present to avoid runtime SQL errors.
            'payment_method_id' => Schema::hasTable('payment_methods') ? 'nullable|exists:payment_methods,id' : 'nullable|integer',
            'notes' => 'nullable|string',
        ];

        // Use validated input from BookingUpdateRequest
        $input = $request->validatedFiltered();

        // Keep snapshot of old values for logging and rules
        $oldValues = $booking->only(array_keys($input));
        // Also snapshot payload_data so we can detect changes made inside it (e.g. pickup_date, delivery_date, notes)
        $oldPayloadData = is_array($booking->payload_data) ? $booking->payload_data : (array) $booking->payload_data;

        // Track attempted status for logging comparison after save
        $attemptedStatus = array_key_exists('status', $input) ? $input['status'] : null;

        // Handle status transition rules
        if (array_key_exists('status', $input) && $input['status'] !== null) {
            $oldStatus = $booking->status;
            $newStatus = $input['status'];

            // Permission check for status changes
            // Allow if admin can either change booking status or has the bookings.edit permission
            $canChangeStatus = ($admin->can('change booking status') || $admin->can('bookings.edit'));
            if ($oldStatus !== $newStatus && !$canChangeStatus) {
                Log::warning('Booking update: status change denied - insufficient permission', ['booking_id' => $booking->id, 'admin_id' => $admin->id ?? null, 'old_status' => $oldStatus, 'attempted_status' => $newStatus, 'request_input' => $request->except(['_token','_method'])]);
                // Flash a structured permission alert so the UI can show a modal explaining the check
                return redirect()->back()->with(['permission_denied' => ['title' => 'Permission denied', 'message' => 'You do not have permission to change booking status. We attempted to change the status to: ' . $newStatus, 'attempted_status' => $newStatus]]);
            }

            // Define allowed progression order
            $order = ['pending' => 0, 'pickup' => 1, 'delivered' => 2, 'canceled' => 3];
            $oldIndex = $order[$oldStatus] ?? 0;
            $newIndex = $order[$newStatus] ?? 0;

            // Prevent reverting to an earlier state
            if ($newIndex < $oldIndex) {
                Log::warning('Booking update: invalid status transition prevented', ['booking_id' => $booking->id, 'admin_id' => $admin->id ?? null, 'old_status' => $oldStatus, 'attempted_status' => $newStatus, 'request_input' => $request->except(['_token','_method'])]);
                // Flash a structured transition alert so UI can show an explanatory modal
                return redirect()->back()->with(['transition_denied' => [
                    'title' => 'Invalid status transition',
                    'message' => 'You cannot revert the booking status to an earlier state.',
                    'old_status' => $oldStatus,
                    'attempted_status' => $newStatus
                ]]);
            }
        }

        // Apply changes inside DB transaction for better consistency and logging
        $payloadData = is_array($booking->payload_data) ? $booking->payload_data : (array) $booking->payload_data;
        $table = $booking->getTable();
        $hasPayloadColumn = Schema::hasColumn($table, 'payload_data');

        DB::beginTransaction();
        try {
            foreach ($input as $k => $v) {
                if ($v === null) continue; // skip nulls to avoid overwriting with null

                if (Schema::hasColumn($table, $k)) {
                    $booking->{$k} = $v;
                } else {
                    if ($hasPayloadColumn) {
                        $payloadData[$k] = $v;
                    } else {
                        Log::warning('Booking update: attempted to set non-column attribute', ['booking_id' => $booking->id, 'attribute' => $k, 'admin_id' => $admin->id ?? null]);
                    }
                }
            }

            if ($hasPayloadColumn) {
                $booking->payload_data = $payloadData;
            }

            // If admin set a lab via the edit form and it was previously empty, set lab_assigned_at
            if (array_key_exists('lab_id', $input)) {
                $oldLab = $oldValues['lab_id'] ?? null;
                $newLab = $input['lab_id'];
                $beforeAssignedAt = $booking->lab_assigned_at ?? null;
                Log::info('Booking update: lab_id present in input', ['booking_id' => $booking->id, 'old_lab' => $oldLab, 'new_lab' => $newLab, 'before_lab_assigned_at' => $beforeAssignedAt]);

                // Set lab_assigned_at when lab changed or when it was previously unset
                if ((string)$oldLab !== (string)$newLab || empty($beforeAssignedAt)) {
                    $booking->lab_assigned_at = now();
                    Log::info('Booking update: setting lab_assigned_at', ['booking_id' => $booking->id, 'lab_assigned_at' => $booking->lab_assigned_at->toDateTimeString()]);
                }
            }

            $saved = $booking->save();

            if (!$saved) {
                // Unexpected failure to save - log and rollback
                DB::rollBack();
                Log::error('Booking update: save returned false', ['booking_id' => $booking->id, 'admin_id' => $admin->id ?? null, 'input' => $input, 'old_values' => $oldValues]);
                return redirect()->back()->with('error', 'Failed to update booking (no DB write).');
            }

            // Compute changes for logging, including payload_data keys
            $changed = [];
            foreach ($input as $k => $v) {
                // If the attribute is a real table column, compare directly
                if (Schema::hasColumn($table, $k)) {
                    $old = array_key_exists($k, $oldValues) ? $oldValues[$k] : null;
                    $new = $booking->{$k} ?? null;
                    if ((string)$old !== (string)$new) {
                        $changed[$k] = ['old' => $old, 'new' => $new];
                    }
                    continue;
                }

                // Otherwise it was written into payload_data; compare against oldPayloadData
                $old = array_key_exists($k, $oldPayloadData) ? $oldPayloadData[$k] : null;
                // After save, $booking->payload_data may be array or JSON; fetch fresh copy
                $freshPayload = is_array($booking->payload_data) ? $booking->payload_data : (array) $booking->payload_data;
                $new = array_key_exists($k, $freshPayload) ? $freshPayload[$k] : null;
                if ((string)$old !== (string)$new) {
                    $changed[$k] = ['old' => $old, 'new' => $new];
                }
            }

            // Structured recording
            if (!empty($changed)) {
                try {
                    $booking = $booking->fresh();
                    $booking->load(['service', 'user', 'lab', 'driver']);

                    $payload = [
                        'booking_id' => $booking->id,
                        'order_number' => $booking->order_number ?? null,
                        'changes' => $changed,
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
                        'admin' => [
                            'id' => $admin->id ?? null,
                            'name' => $admin->name ?? null,
                        ],
                        'request_input' => $request->except(['_token', '_method']),
                        'route' => optional(request()->route())->getName() ?? null,
                    ];

                    $actorType = 'system'; $actorId = null; $actorName = 'System';
                    if (!empty($admin)) { $actorType = 'admin'; $actorId = $admin->id; $actorName = $admin->name ?? ($admin->email ?? null); }

                    SystemLogger::record([
                        'actor_type' => $actorType,
                        'actor_id' => $actorId,
                        'actor_name' => $actorName,
                        'event_type' => 'booking',
                        'event_subtype' => 'updated',
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'payload' => $payload,
                    ]);
                } catch (\Exception $e) {
                    Log::error('SystemLogger failed recording booking update', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'admin_id' => $admin->id ?? null]);
                }
            }

            DB::commit();

            // Log booking row after commit to verify timestamps persisted
            try {
                $after = $booking->fresh();
                Log::info('Booking update: after commit snapshot', ['booking_id' => $booking->id, 'lab_id' => $after->lab_id ?? null, 'lab_assigned_at' => optional($after->lab_assigned_at)->toDateTimeString()]);
            } catch (\Exception $e) {
                Log::warning('Booking update: failed serializing booking after commit', ['booking_id' => $booking->id, 'error' => $e->getMessage()]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking update: exception while saving', ['booking_id' => $booking->id, 'admin_id' => $admin->id ?? null, 'attempted_status' => $attemptedStatus, 'input' => $input, 'old_values' => $oldValues, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Failed to update booking (see logs).');
        }

        if (!empty($changed)) {
            try {
                $booking = $booking->fresh();
                $booking->load(['service', 'user', 'lab', 'driver']);

                $payload = [
                    'booking_id' => $booking->id,
                    'order_number' => $booking->order_number ?? null,
                    'changes' => $changed,
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
                    'admin' => [
                        'id' => $admin->id ?? null,
                        'name' => $admin->name ?? null,
                    ],
                    'request_input' => $request->except(['_token', '_method']),
                    'route' => optional(request()->route())->getName() ?? null,
                ];

                $actorType = 'system'; $actorId = null; $actorName = 'System';
                if (!empty($admin)) { $actorType = 'admin'; $actorId = $admin->id; $actorName = $admin->name ?? ($admin->email ?? null); }

                SystemLogger::record([
                    'actor_type' => $actorType,
                    'actor_id' => $actorId,
                    'actor_name' => $actorName,
                    'event_type' => 'booking',
                    'event_subtype' => 'updated',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'payload' => $payload,
                ]);
            } catch (\Exception $e) {
                Log::error('SystemLogger failed recording booking update', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'admin_id' => $admin->id ?? null]);
            }
        }

        // If status changed, send notifications as before (user only)
        if (array_key_exists('status', $changed) && $booking->user) {
            $newStatus = $changed['status']['new'];
            $notificationClass = match ($newStatus) {
                'pending' => \App\Notifications\PendingBookingNotification::class,
                'pickup' => \App\Notifications\PickupBookingNotification::class,
                'delivered' => \App\Notifications\DeliveredBookingNotification::class,
                'canceled' => \App\Notifications\CanceledBookingNotification::class,
                default => null,
            };
            if ($notificationClass) {
                \App\Services\NotificationService::safeNotify($booking->user, new $notificationClass($booking), ['booking_id' => $booking->id]);
            }
        }

        // Send separate notifications per changed field to all parties (admin + user + driver + lab)
        if (!empty($changed)) {
            $booking = $booking->fresh();
            $booking->load(['user', 'lab', 'driver', 'pickupDriver', 'deliveryDriver']);

            $recipients = [];
            $addRecipient = function ($model) use (&$recipients) {
                if (!$model || !isset($model->id)) return;
                $key = get_class($model) . ':' . $model->id;
                $recipients[$key] = $model;
            };

            // Admins (all)
            foreach (\App\Models\Admin::all() as $adminRecipient) {
                $addRecipient($adminRecipient);
            }

            // User + lab + drivers
            $addRecipient($booking->user ?? null);
            $addRecipient($booking->lab ?? null);
            $addRecipient($booking->driver ?? null);
            $addRecipient($booking->pickupDriver ?? null);
            $addRecipient($booking->deliveryDriver ?? null);

            foreach ($changed as $field => $vals) {
                Log::info('Booking update: notifying field change', [
                    'booking_id' => $booking->id,
                    'field' => $field,
                    'old' => $vals['old'] ?? null,
                    'new' => $vals['new'] ?? null,
                    'recipients' => count($recipients),
                ]);
                foreach ($recipients as $recipient) {
                    // Avoid duplicate status notification for user (already sent above)
                    if ($field === 'status' && $booking->user && get_class($recipient) === get_class($booking->user) && $recipient->id === $booking->user->id) {
                        continue;
                    }
                    \App\Services\NotificationService::safeNotify(
                        $recipient,
                        new \App\Notifications\BookingFieldsUpdated($booking, [$field => $vals]),
                        ['booking_id' => $booking->id, 'field' => $field, 'notifiable' => get_class($recipient)]
                    );
                }
            }
        }

        Log::info('Booking update: End, redirecting', ['booking_id' => $booking->id]);
        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Booking updated successfully.');
    }


    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }

    /**
     * Assign a driver to a booking (only for category id 1 / dry clean)
     */
    public function assignDriver(Request $request, Booking $booking)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        // Only allow assigning drivers for service category id == 1 (dry clean)
        if (($booking->service_category_id ?? null) !== 1) {
            return redirect()->back()->with('error', 'Driver assignment is only allowed for Dry Clean bookings.');
        }

        $request->validate([
            'driver_id' => 'nullable|exists:drivers,id'
        ]);

        // For backward compatibility keep 'driver_id' mapping, but store into pickup_driver_id
        $booking->pickup_driver_id = $request->input('driver_id');
        // also keep legacy driver_id for older code paths
        $booking->driver_id = $request->input('driver_id');
        $booking->save();

        // Optionally notify driver
        // Notify pickup driver if contact available
        if ($booking->pickupDriver && $booking->pickupDriver->email) {
            try {
                $booking->pickupDriver->notify(new \App\Notifications\BookingStatusUpdated($booking));
            } catch (\Exception $e) {
                Log::error('Failed to notify pickup driver on assignment', ['error' => $e->getMessage(), 'booking' => $booking->id]);
            }
        }

        // Notify user that a driver has been assigned
        if ($booking->user) {
            try {
                $booking->user->notify(new \App\Notifications\DriverAssignedNotification($booking));
            } catch (\Exception $e) {
                Log::error('Failed to notify user on driver assignment', ['error' => $e->getMessage(), 'booking' => $booking->id]);
            }
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Driver assigned successfully.');
    }

    /**
     * Assign lab from admin dashboard
     */
    public function assignLab(Request $request, Booking $booking)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return redirect()->route('admin.login');

        // Do not allow assigning a lab for service id 3
        if (($booking->service_id ?? null) === 3) {
            return redirect()->route('admin.bookings.show', $booking)->with('error', 'Lab assignment is not applicable for this service.');
        }

        // Log the incoming payload and booking row to help debug missing timestamps
        Log::info('AssignLab: incoming request', ['admin_id' => $admin->id ?? null, 'booking_id' => $booking->id, 'input' => $request->all(), 'request_id' => $request->header('X-Request-Id') ?? null]);

        $request->validate(['lab_id' => 'required|exists:labs,id']);

        // Snapshot before changes
        try {
            Log::info('AssignLab: before save', ['booking' => $booking->toArray()]);
        } catch (\Exception $e) {
            Log::warning('AssignLab: failed serializing booking before save', ['error' => $e->getMessage(), 'booking_id' => $booking->id]);
        }

        $booking->lab_id = $request->lab_id;
        $booking->lab_assigned_at = now();

        try {
            $saved = $booking->save();
            Log::info('AssignLab: save result', ['booking_id' => $booking->id, 'saved' => (bool)$saved]);
        } catch (\Exception $e) {
            Log::error('AssignLab: exception while saving booking', ['booking_id' => $booking->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.bookings.show', $booking)->with('error', 'Failed to assign lab (see logs).');
        }

        // Re-load fresh record and log after save for verification
        try {
            $fresh = $booking->fresh();
            Log::info('AssignLab: after save', ['booking' => $fresh ? $fresh->toArray() : null]);
        } catch (\Exception $e) {
            Log::warning('AssignLab: failed serializing booking after save', ['error' => $e->getMessage(), 'booking_id' => $booking->id]);
        }

        if ($booking->user) {
            try { $booking->user->notify(new \App\Notifications\LabAssignedNotification($booking)); } catch (\Exception $e) { Log::error($e->getMessage()); }
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Lab assigned.');
    }

    public function markArrivedAtLab(Request $request, Booking $booking)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return redirect()->route('admin.login');

        // Prevent lab actions for service id 3
        if (($booking->service_id ?? null) === 3) {
            return redirect()->route('admin.bookings.show', $booking)->with('error', 'Lab actions are not applicable for this service.');
        }

        if (!$booking->lab_id && $request->has('lab_id')) {
            $booking->lab_id = $request->lab_id;
            $booking->lab_assigned_at = now();
        }
        $booking->lab_arrived_at = now();
        $booking->save();

        if ($booking->user) {
            try { $booking->user->notify(new \App\Notifications\LabArrivedNotification($booking)); } catch (\Exception $e) { Log::error($e->getMessage()); }
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Marked arrived at lab.');
    }

    public function markPickedFromLab(Request $request, Booking $booking)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return redirect()->route('admin.login');

        // Prevent lab actions for service id 3
        if (($booking->service_id ?? null) === 3) {
            return redirect()->route('admin.bookings.show', $booking)->with('error', 'Lab actions are not applicable for this service.');
        }

        if (!$booking->lab_id && $request->has('lab_id')) {
            $booking->lab_id = $request->lab_id;
            $booking->lab_assigned_at = now();
        }
        $booking->lab_picked_at = now();
        $booking->save();

        if ($booking->user) {
            try { $booking->user->notify(new \App\Notifications\LabPickedNotification($booking)); } catch (\Exception $e) { Log::error($e->getMessage()); }
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Marked picked from lab.');
    }

    public function markDriverCollected(Request $request, Booking $booking)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return redirect()->route('admin.login');

        $booking->driver_collected_at = now();
        $booking->save();

        if ($booking->user) {
            try { $booking->user->notify(new \App\Notifications\DriverCollectedNotification($booking)); } catch (\Exception $e) { Log::error($e->getMessage()); }
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Marked driver collected.');
    }

    public function markReturnedToUser(Request $request, Booking $booking)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return redirect()->route('admin.login');

        $booking->driver_returned_at = now();
        if (strtolower($booking->status ?? '') !== 'delivered') {
            $booking->status = 'delivered';
        }
        $booking->save();

        if ($booking->user) {
            try { $booking->user->notify(new \App\Notifications\DriverReturnedNotification($booking)); } catch (\Exception $e) { Log::error($e->getMessage()); }
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Marked returned to user.');
    }

    public function trashed()
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $bookings = Booking::onlyTrashed()->with(['user', 'service', 'serviceCategory', 'serviceType'])->get();
        return view('dashboard.admin.bookings.trashed', compact('bookings', 'admin'));
    }

    public function restore($id)
    {
        $booking = Booking::onlyTrashed()->findOrFail($id);
        $booking->restore();
        return redirect()->route('admin.bookings.trashed')->with('success', 'Booking restored successfully.');
    }

    /**
     * Show a form allowing admin to write a custom message for the booking's user.
     */
    public function notifyForm(Booking $booking)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return redirect()->route('admin.login');

        $booking->load(['user']);
        return view('dashboard.admin.bookings.notify', compact('booking', 'admin'));
    }

    /**
     * POST handler to send a custom notification to the booking user.
     */
    public function sendCustomNotification(Request $request, Booking $booking)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return redirect()->route('admin.login');

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $booking->load(['user']);

        if (!$booking->user) {
            return redirect()->back()->with('error', 'Booking has no associated user to notify.');
        }

        try {
            $payload = [
                'booking_id' => $booking->id,
                'admin_id' => $admin->id ?? null,
                'title' => $data['title'] ?? null,
            ];

            \App\Services\NotificationService::safeNotify($booking->user, new \App\Notifications\AdminCustomMessage($booking, $data['message'], $data['title'] ?? null), $payload);

            // Record in system logs for audit
            SystemLogger::record([
                'actor_type' => 'admin',
                'actor_id' => $admin->id ?? null,
                'actor_name' => $admin->name ?? null,
                'event_type' => 'notification',
                'event_subtype' => 'admin_custom_message',
                'ip_address' => $request->ip(),
                'payload' => ['booking_id' => $booking->id, 'title' => $data['title'] ?? null],
            ]);

            return redirect()->route('admin.bookings.show', $booking)->with('success', 'Custom notification sent.');
        } catch (\Exception $e) {
            Log::error('Failed to send admin custom message', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'admin_id' => $admin->id ?? null]);
            return redirect()->back()->with('error', 'Failed to send notification (see logs).');
        }
    }

    /**
     * Global search for booking by user name or phone.
     */
    public function search(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }
        $q = $request->input('q');
        if (!$q) {
            return redirect()->back()->with('error', 'Please enter a search term.');
        }
        // First try to match by order number directly
        $booking = Booking::where('order_number', 'like', "%$q%")->first();

        // If not found by order number, search by user fields
        if (!$booking) {
            $booking = Booking::whereHas('user', function ($query) use ($q) {
                $query->where('name', 'like', "%$q%")
                    ->orWhere('phone', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
                ;
            })->first();
        }
        if ($booking) {
            return redirect()->route('admin.bookings.show', $booking->id);
        }
        return redirect()->back()->with('error', 'No booking found for this user.');
    }

    /**
     * Export bookings to Excel with filters
     */
    public function export(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        $filename = 'bookings_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new BookingsExport($request), $filename);
    }

    /**
     * Generate or stream invoice PDF for a booking.
     * Accepts optional ?status= to include a chosen new status alongside the existing one.
     */
    public function invoicePdf(Request $request, Booking $booking)
    {
        $admin = Auth::guard('admin')->user();
        if (!$admin) return redirect()->route('admin.login');

        // Load relations and payload
        $booking->load(['user', 'service', 'paymentMethod', 'lab', 'driver']);
        $payload = is_array($booking->payload_data) ? $booking->payload_data : (array) $booking->payload_data;

        $oldStatus = $booking->status;
        $chosen = $request->query('status');
        $newStatus = $chosen ?? $oldStatus;

        // Detect car-wash bookings (by payload or service name/id) to use a dedicated PDF template
        $isCarWash = false;
        if (isset($payload['car_wash_type']) || isset($payload['number_of_cars']) || isset($payload['cars_additional_services']) || isset($payload['additional_services'])) {
            $isCarWash = true;
        } elseif (($booking->service_id ?? null) === 3) {
            $isCarWash = true;
        } elseif ($booking->service && is_array(data_get($booking, 'service.name'))) {
            $name = data_get($booking, 'service.name')[app()->getLocale()] ?? '';
            if (stripos($name, 'car') !== false) $isCarWash = true;
        } elseif ($booking->service && is_string(data_get($booking, 'service.name'))) {
            if (stripos($booking->service->name, 'car') !== false) $isCarWash = true;
        }

        $viewData = compact('booking', 'payload', 'oldStatus', 'newStatus', 'admin', 'isCarWash');

        // Choose a dedicated view for car-wash invoices for clearer layout
        $viewName = $isCarWash ? 'dashboard.admin.bookings.invoice_pdf_carwash' : 'dashboard.admin.bookings.invoice_pdf';

        // Render blade to HTML
        $html = view($viewName, $viewData)->render();

        // Prefer the Laravel ArPdf package (if installed), then mPDF (recommended), then Gpdf, then DomPDF fallback.
        try {
            // If package registers an 'arpdf' container binding or facade, prefer it for Arabic PDFs
            try {
                if (app()->bound('arpdf')) {
                    try {
                        $arpdf = app('arpdf');
                        if (method_exists($arpdf, 'streamHtml')) {
                            return $arpdf->streamHtml($html, "invoice-{$booking->order_number}.pdf");
                        }
                        if (method_exists($arpdf, 'downloadHtml')) {
                            return $arpdf->downloadHtml($html, "invoice-{$booking->order_number}.pdf");
                        }
                    } catch (\Exception $e) {
                        Log::warning('InvoicePdf: arpdf binding present but call failed', ['error' => $e->getMessage()]);
                    }
                }
                // Try common facade name if present
                if (class_exists('ArPdf')) {
                    try {
                        $arpdfClass = 'ArPdf';
                        if (method_exists($arpdfClass, 'streamHtml')) {
                            return $arpdfClass::streamHtml($html, "invoice-{$booking->order_number}.pdf");
                        }
                        if (method_exists($arpdfClass, 'downloadHtml')) {
                            return $arpdfClass::downloadHtml($html, "invoice-{$booking->order_number}.pdf");
                        }
                    } catch (\Exception $e) {
                        Log::warning('InvoicePdf: ArPdf facade present but call failed', ['error' => $e->getMessage()]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('InvoicePdf: arpdf detection error', ['error' => $e->getMessage()]);
            }
            if (class_exists('\Mpdf\\Mpdf')) {
                try {
                    $mpdf = new \Mpdf\Mpdf([ 'mode' => 'utf-8', 'format' => 'A4', 'margin_left' => 12, 'margin_right' => 12, 'default_font' => 'dejavusans' ]);
                    $mpdf->WriteHTML($html);
                    return $mpdf->Output("invoice-{$booking->order_number}.pdf", \Mpdf\Output\Destination::INLINE);
                } catch (\Exception $e) {
                    Log::warning('InvoicePdf: mPDF failed, falling back', ['error' => $e->getMessage()]);
                }
            }

            if (class_exists('Gpdf\\Gpdf')) {
                try {
                    $gpdfClass = 'Gpdf\\Gpdf';
                    $gpdf = new $gpdfClass();
                    if (method_exists($gpdf, 'setOption')) {
                        $gpdf->setOption('isHtml5ParserEnabled', true);
                    }
                    if (method_exists($gpdf, 'setPaper')) {
                        $gpdf->setPaper('A4', 'portrait');
                    }
                    if (method_exists($gpdf, 'loadHtml')) {
                        $gpdf->loadHtml($html);
                    }
                    if (method_exists($gpdf, 'stream')) {
                        return $gpdf->stream("invoice-{$booking->order_number}.pdf");
                    }
                } catch (\Exception $e) {
                    Log::warning('InvoicePdf: Gpdf failed, falling back', ['error' => $e->getMessage()]);
                }
            }

            // barryvdh/laravel-dompdf fallback
            if (class_exists('Barryvdh\DomPDF\Facade\Pdf') || function_exists('app')) {
                try {
                    $pdf = app('dompdf.wrapper');
                    $pdf->loadHTML($html);
                    return $pdf->stream("invoice-{$booking->order_number}.pdf");
                } catch (\Exception $e) {
                    Log::warning('InvoicePdf: dompdf.wrapper failed, falling back to raw HTML', ['error' => $e->getMessage()]);
                }
            }
        } catch (\Exception $e) {
            Log::error('InvoicePdf generation failed', ['error' => $e->getMessage(), 'booking_id' => $booking->id]);
        }

        // If PDF libs not available, return HTML view so developer can see output
        return response($html);
    }
}
