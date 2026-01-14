<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('role:super-admin')->only(['index','destroyAll']);
    }
    public function index(Request $request)
    {
        $query = SystemLog::query();

        if ($request->filled('actor_type')) {
            $query->where('actor_type', $request->query('actor_type'));
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->query('event_type'));
        }

        // Enhanced free-text search: search actor_name, payload, event_type and event_subtype
        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function($qb) use ($q) {
                $qb->where('actor_name', 'like', "%{$q}%")
                   ->orWhere('actor_id', 'like', "%{$q}%")
                   ->orWhere('event_type', 'like', "%{$q}%")
                   ->orWhere('event_subtype', 'like', "%{$q}%")
                   ->orWhere('payload', 'like', "%{$q}%");
            });
        }

        // Date range filtering (created_at)
        if ($request->filled('date_from') || $request->filled('date_to')) {
            try {
                $from = $request->filled('date_from') ? Carbon::parse($request->query('date_from'))->startOfDay() : null;
                $to = $request->filled('date_to') ? Carbon::parse($request->query('date_to'))->endOfDay() : null;

                if ($from && $to) {
                    $query->whereBetween('created_at', [$from, $to]);
                } elseif ($from) {
                    $query->where('created_at', '>=', $from);
                } elseif ($to) {
                    $query->where('created_at', '<=', $to);
                }
            } catch (\Exception $e) {
                // If parsing fails, ignore date filters rather than throw
            }
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        return view('dashboard.admin.logs.index', compact('logs'));
    }

    /**
     * Delete all system logs.
     */
    public function destroyAll(Request $request)
    {
        // extra safety: require confirmation param
        try {
            \App\Models\SystemLog::query()->delete();
            return redirect()->route('admin.logs.index')->with('success', 'All logs deleted');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to clear system logs: ' . $e->getMessage());
            return redirect()->route('admin.logs.index')->with('error', 'Failed to delete logs');
        }
    }
}
