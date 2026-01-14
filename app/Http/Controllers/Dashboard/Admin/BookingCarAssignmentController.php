<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\DriverVehicle;
use App\Models\BookingCarAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookingCarAssignmentController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'driver_vehicle_id' => 'required|exists:driver_vehicles,id',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        // Allow this action only for Car Wash bookings (service id 3 or name contains 'car')
        $isCarWash = false;
        if ($booking->service_id === 3) {
            $isCarWash = true;
        } elseif ($booking->service && is_array($booking->service->name)) {
            $name = $booking->service->name[app()->getLocale()] ?? '';
            if (stripos($name, 'car') !== false) $isCarWash = true;
        } elseif ($booking->service && is_string($booking->service->name)) {
            if (stripos($booking->service->name, 'car') !== false) $isCarWash = true;
        }

        if (! $isCarWash) {
            return back()->withErrors(['driver_vehicle_id' => 'This booking is not a car-wash service.']);
        }

        // create or update assignment for this booking
        $assignment = BookingCarAssignment::where('booking_id', $booking->id)->first();
        if ($assignment) {
            $assignment->driver_vehicle_id = $data['driver_vehicle_id'];
            $assignment->assigned_by = Auth::guard('admin')->id() ?? Auth::id();
            $assignment->start_at = $data['start_at'] ?? null;
            $assignment->end_at = $data['end_at'] ?? null;
            $assignment->save();
        } else {
            $assignment = BookingCarAssignment::create([
                'booking_id' => $booking->id,
                'driver_vehicle_id' => $data['driver_vehicle_id'],
                'assigned_by' => Auth::guard('admin')->id() ?? Auth::id(),
                'start_at' => $data['start_at'] ?? null,
                'end_at' => $data['end_at'] ?? null,
            ]);
        }

        // Notify booking user about the assigned car and timeslot
        try {
            if ($booking->user) {
                $booking->user->notify(new \App\Notifications\CarAssignedNotification($booking, $assignment));
            }
        } catch (\Exception $e) {
            // don't break the flow if notification fails; log and continue
            Log::error('Failed to notify user of car assignment: ' . $e->getMessage(), ['booking_id' => $booking->id]);
        }

        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Car assigned to booking successfully. User has been notified.');
    }
}
