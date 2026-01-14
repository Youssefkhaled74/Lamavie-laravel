<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Area;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $appointments = Booking::count();
        $totalRevenue = Booking::sum('total');
        $averageOrderValue = Booking::avg('total') ?? 0;
        // bookings by status (e.g., pending, completed, canceled)
        $bookingsByStatus = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        $services = Service::count();
        $serviceCategories = ServiceCategory::count();
    $areas = Area::all();

        // User growth for the last 7, 30, and 90 days (daily breakdown)
        $userGrowth = [
            'last7' => User::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'last30' => User::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'last90' => User::where('created_at', '>=', Carbon::now()->subDays(90))->count(),
            'daily' => [
                'last7' => $this->getUserGrowthByDay(7),
                'last30' => $this->getUserGrowthByDay(30),
                'last90' => $this->getUserGrowthByDay(90),
            ]
        ];

        $bookingsOverTime = $this->getBookingsByDay(30); // last 30 days

        // Debug logging (only in non-production) to help trace empty data issues.
        if (config('app.env') !== 'production') {
            try {
                Log::info('Dashboard stats', [
                    'totalRevenue' => $totalRevenue,
                    'averageOrderValue' => $averageOrderValue,
                    'bookingsByStatus' => $bookingsByStatus,
                    'bookingsOverTime_sample' => $bookingsOverTime->take(5)->values()->toArray(),
                ]);
            } catch (\Throwable $e) {
                // swallow logging errors
            }
        }

        return view('dashboard.admin.welcome', [
            'admin' => Auth::guard('admin')->user(),
            'totalUsers' => $totalUsers,
            'appointments' => $appointments,
            'services' => $services,
            'serviceCategories' => $serviceCategories,
            'userGrowth' => $userGrowth,
            'totalRevenue' => $totalRevenue,
            'averageOrderValue' => $averageOrderValue,
            'bookingsByStatus' => $bookingsByStatus,
            'bookingsOverTime' => $bookingsOverTime,
            'areas' => $areas,
        ]);
    }

    /**
     * Get user registrations per day for the last N days.
     */
    private function getUserGrowthByDay($days)
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $dates = collect();
        for ($i = 0; $i < $days; $i++) {
            $dates->push($start->copy()->addDays($i)->format('Y-m-d'));
        }
        $users = User::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');
        return $dates->map(function ($date) use ($users) {
            return [
                'date' => $date,
                'count' => (int)($users[$date] ?? 0)
            ];
        });
    }

    /**
     * Get bookings per day for the last N days.
     */
    private function getBookingsByDay($days)
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $dates = collect();
        for ($i = 0; $i < $days; $i++) {
            $dates->push($start->copy()->addDays($i)->format('Y-m-d'));
        }
        $bookings = Booking::where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, COALESCE(SUM(total),0) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        return $dates->map(function ($date) use ($bookings) {
            $row = $bookings->get($date);
            return [
                'date' => $date,
                'count' => (int)($row->count ?? 0),
                'revenue' => (float)($row->revenue ?? 0),
            ];
        });
    }
}