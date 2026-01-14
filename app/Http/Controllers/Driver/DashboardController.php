<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Driver;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $driver = Auth::guard('driver')->user();
        if (!$driver) {
            return redirect()->route('driver.login');
        }

    // Use loaded relation for service ids
    $serviceIds = $driver->services->pluck('id')->toArray();

        $bookingsQuery = Booking::whereIn('service_id', $serviceIds)->latest();

        if ($request->has('q')) {
            $bookingsQuery->where(function($q) use ($request) {
                $q->whereHas('user', function($uq) use ($request) {
                    $uq->where('name', 'like', '%'.$request->q.'%')->orWhere('phone', 'like', '%'.$request->q.'%');
                })->orWhere('order_number', 'like', '%'.$request->q.'%');
            });
        }

        $bookings = $bookingsQuery->paginate(12);

        return view('driver.dashboard.index', compact('bookings', 'driver'));
    }
}
