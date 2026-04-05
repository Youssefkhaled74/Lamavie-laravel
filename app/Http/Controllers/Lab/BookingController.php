<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Services\SystemLogger;

class BookingController extends Controller
{
    /**
     * Mark that the lab has received the booking (arrived at lab)
     */
    public function markArrivedAtLab(Request $request, Booking $booking)
    {
        $lab = Auth::guard('lab')->user();
        if (!$lab) {
            return redirect()->route('lab.login');
        }

        if ($booking->lab_id !== $lab->id) {
            abort(403, 'You are not authorized to modify this booking.');
        }

    // Prevent lab actions for service id 3
    if (($booking->service_id ?? null) === 3) {
        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'Lab actions are not applicable for this service.'], 422);
        }
        return redirect()->route('lab.bookings.show', $booking)->with('error', 'Lab actions are not applicable for this service.');
    }

    $booking->lab_arrived_at = now();
    $this->moveToPickupIfApplicable($booking);
    $booking->save();

        // Log lab arrived action
        try {
            SystemLogger::record([
                'actor_type' => 'lab',
                'actor_id' => $lab->id,
                'actor_name' => $lab->name ?? ($lab->email ?? ($lab->phone ?? null)),
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
            Log::error('SystemLogger failed recording lab lab_arrived', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'lab_id' => $lab->id]);
        }
        if ($booking->user) {
            try {
                $booking->user->notify(new \App\Notifications\LabArrivedNotification($booking));
            } catch (\Exception $e) {
                Log::error('Lab markArrivedAtLab: Failed to send LabArrivedNotification', [
                    'error' => $e->getMessage(),
                    'booking_id' => $booking->id,
                    'lab_id' => $lab->id,
                ]);
            }
        }

        // If AJAX/JSON requested, return minimal booking payload
        if ($request->wantsJson() || $request->expectsJson() || $request->ajax()) {
            $booking->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Marked as arrived at lab.',
                'booking' => [
                    'id' => $booking->id,
                    'lab_id' => $booking->lab_id,
                    'lab_assigned_at' => $booking->lab_assigned_at?->toDateTimeString(),
                    'lab_arrived_at' => $booking->lab_arrived_at?->toDateTimeString(),
                    'lab_picked_at' => $booking->lab_picked_at?->toDateTimeString(),
                    'driver_collected_at' => $booking->driver_collected_at?->toDateTimeString(),
                    'status' => $booking->status,
                    'lab' => $booking->lab ? ['id' => $booking->lab->id, 'name' => $booking->lab->name, 'phone' => $booking->lab->phone] : null,
                ],
            ]);
        }

        return redirect()->route('lab.bookings.show', $booking)->with('success', 'Marked as arrived at lab.');
    }

    /**
     * Mark that the lab has finished processing and the booking was picked up from the lab
     */
    public function markPickedFromLab(Request $request, Booking $booking)
    {
        $lab = Auth::guard('lab')->user();
        if (!$lab) {
            return redirect()->route('lab.login');
        }

        if ($booking->lab_id !== $lab->id) {
            abort(403, 'You are not authorized to modify this booking.');
        }

    // Prevent lab actions for service id 3
    if (($booking->service_id ?? null) === 3) {
        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'Lab actions are not applicable for this service.'], 422);
        }
        return redirect()->route('lab.bookings.show', $booking)->with('error', 'Lab actions are not applicable for this service.');
    }

    $booking->lab_picked_at = now();
    $this->moveToPickupIfApplicable($booking);
    $booking->save();

        // Log lab picked action
        try {
            SystemLogger::record([
                'actor_type' => 'lab',
                'actor_id' => $lab->id,
                'actor_name' => $lab->name ?? ($lab->email ?? ($lab->phone ?? null)),
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
            Log::error('SystemLogger failed recording lab lab_picked', ['error' => $e->getMessage(), 'booking_id' => $booking->id, 'lab_id' => $lab->id]);
        }
        if ($booking->user) {
            try {
                $booking->user->notify(new \App\Notifications\LabPickedNotification($booking));
            } catch (\Exception $e) {
                Log::error('Lab markPickedFromLab: Failed to send LabPickedNotification', [
                    'error' => $e->getMessage(),
                    'booking_id' => $booking->id,
                    'lab_id' => $lab->id,
                ]);
            }
        }

        if ($request->wantsJson() || $request->expectsJson() || $request->ajax()) {
            $booking->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Marked as picked from lab.',
                'booking' => [
                    'id' => $booking->id,
                    'lab_id' => $booking->lab_id,
                    'lab_assigned_at' => $booking->lab_assigned_at?->toDateTimeString(),
                    'lab_arrived_at' => $booking->lab_arrived_at?->toDateTimeString(),
                    'lab_picked_at' => $booking->lab_picked_at?->toDateTimeString(),
                    'driver_collected_at' => $booking->driver_collected_at?->toDateTimeString(),
                    'status' => $booking->status,
                    'lab' => $booking->lab ? ['id' => $booking->lab->id, 'name' => $booking->lab->name, 'phone' => $booking->lab->phone] : null,
                ],
            ]);
        }

        return redirect()->route('lab.bookings.show', $booking)->with('success', 'Marked as picked from lab.');
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
