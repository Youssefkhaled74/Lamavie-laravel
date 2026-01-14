<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverVehicle;
use App\Models\Booking;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VehicleTimelineExport;

class CarTimelineController extends Controller
{
    /**
     * Return JSON data for the car timeline (next N hours)
     */
    public function timelineData(Request $request)
    {
        $now = Carbon::now();
        $hours = intval($request->query('hours', 48));
        $windowEnd = $now->copy()->addHours($hours);

        $vehicles = DriverVehicle::with(['drivers', 'assignments.booking.user'])->orderBy('id', 'desc')->limit(12)->get();

        $result = $vehicles->map(function($v) use ($now, $windowEnd) {
            $assignments = $v->assignments->filter(function($a) use ($windowEnd, $now) {
                $aStart = $a->start_at ? Carbon::parse($a->start_at) : null;
                $aEnd = $a->end_at ? Carbon::parse($a->end_at) : null;
                if (!$aStart && !$aEnd) return false;
                if ($aStart && $aStart->gt($windowEnd)) return false;
                if ($aEnd && $aEnd->lt($now)) return false;
                return true;
            })->map(function($a) {
                return [
                    'id' => $a->id,
                    'booking_id' => $a->booking_id,
                    'start_at' => $a->start_at ? $a->start_at->toIso8601String() : null,
                    'end_at' => $a->end_at ? $a->end_at->toIso8601String() : null,
                    'booking_order' => $a->booking->order_number ?? null,
                    'booking_user' => $a->booking->user->name ?? null,
                ];
            })->values();

            return [
                'id' => $v->id,
                'plate_number' => $v->plate_number,
                'make' => $v->make,
                'model' => $v->model,
                'drivers' => $v->drivers->pluck('name'),
                'assignments' => $assignments,
            ];
        });

        return response()->json(['now' => $now->toIso8601String(), 'hours' => $hours, 'vehicles' => $result]);
    }

    /**
     * Return booking summary JSON used by sidebar modal
     */
    public function bookingJson(Booking $booking)
    {
        $booking->load('user');
        return response()->json([
            'id' => $booking->id,
            'order_number' => $booking->order_number,
            'user' => ['name' => $booking->user->name ?? null, 'phone' => $booking->user->phone ?? null],
            'total' => $booking->total,
            'status' => $booking->status,
        ]);
    }

    /**
     * Display full-page vehicle timeline view
     */
    public function fullTimeline()
    {
        $admin = auth()->guard('admin')->user();
        if (!$admin) {
            return redirect()->route('admin.login');
        }

        return view('dashboard.admin.vehicle_timeline.index');
    }

    /**
     * Export vehicle timeline data as Excel
     */
    public function export(Request $request)
    {
        $now = Carbon::now();
        $hours = intval($request->query('hours', 72));

        $query = DriverVehicle::with(['drivers', 'assignments.booking.user']);
        if ($request->filled('vehicle_id')) {
            $query->where('id', $request->vehicle_id);
        }
        // limit to 50 vehicles to avoid huge exports by default
        $vehicles = $query->orderByDesc('id')->limit(50)->get()->map(function($v) use ($now, $hours) {
            $windowEnd = $now->copy()->addHours($hours);
            $assignments = $v->assignments->filter(function($a) use ($now, $windowEnd) {
                $aStart = $a->start_at ? Carbon::parse($a->start_at) : null;
                $aEnd = $a->end_at ? Carbon::parse($a->end_at) : null;
                if (!$aStart && !$aEnd) return false;
                if ($aStart && $aStart->gt($windowEnd)) return false;
                if ($aEnd && $aEnd->lt($now)) return false;
                return true;
            })->map(function($a) {
                return [
                    'id' => $a->id,
                    'booking_id' => $a->booking_id,
                    'booking_order' => $a->booking->order_number ?? null,
                    'booking_user' => $a->booking->user->name ?? null,
                    'start_at' => $a->start_at ? $a->start_at->toDateTimeString() : null,
                    'end_at' => $a->end_at ? $a->end_at->toDateTimeString() : null,
                    'notes' => $a->notes ?? null,
                    'status' => $a->status ?? null,
                    'booking' => $a->booking ? [
                        'id' => $a->booking->id,
                        'order_number' => $a->booking->order_number ?? null,
                        'user' => ['name' => $a->booking->user->name ?? null, 'phone' => $a->booking->user->phone ?? null, 'email' => $a->booking->user->email ?? null],
                        'total' => $a->booking->total ?? null,
                        'status' => $a->booking->status ?? null,
                        'created_at' => $a->booking->created_at ? $a->booking->created_at->toDateTimeString() : null,
                        'payload_data' => is_array($a->booking->payload_data ?? null) ? $a->booking->payload_data : (is_string($a->booking->payload_data ?? null) ? (json_decode($a->booking->payload_data, true) ?: []) : []),
                    ] : null,
                ];
            })->values();

            return [
                'id' => $v->id,
                'plate_number' => $v->plate_number,
                'make' => $v->make,
                'model' => $v->model,
                'drivers' => $v->drivers->pluck('name')->toArray(),
                'assignments' => $assignments->toArray(),
            ];
        })->toArray();

        $fileName = 'vehicle-timeline-' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new VehicleTimelineExport($vehicles), $fileName);
    }
}
