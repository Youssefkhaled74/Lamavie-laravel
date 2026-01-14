<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $lab = Auth::guard('lab')->user();
        if (!$lab) {
            return redirect()->route('lab.login');
        }

        $bookingsQuery = Booking::with(['user', 'service'])->where('lab_id', $lab->id)->latest();

        if ($request->has('q')) {
            $bookingsQuery->where(function($q) use ($request) {
                $q->whereHas('user', function($uq) use ($request) {
                    $uq->where('name', 'like', '%'.$request->q.'%')->orWhere('phone', 'like', '%'.$request->q.'%');
                })->orWhere('order_number', 'like', '%'.$request->q.'%');
            });
        }

        $bookings = $bookingsQuery->paginate(12);

        return view('lab.dashboard.index', compact('bookings', 'lab'));
    }

    public function showBooking(Booking $booking)
    {
        $lab = Auth::guard('lab')->user();
        if (!$lab) {
            return redirect()->route('lab.login');
        }

        if ($booking->lab_id !== $lab->id) {
            abort(403, 'You are not authorized to view this booking.');
        }

        $booking->load(['user', 'service', 'serviceCategory', 'serviceType']);
        return view('lab.bookings.show', compact('booking', 'lab'));
    }
}
