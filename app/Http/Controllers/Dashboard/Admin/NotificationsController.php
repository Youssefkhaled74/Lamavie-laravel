<?php

namespace App\Http\Controllers\Dashboard\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Notification as NotificationModel;
use Illuminate\Support\Facades\Log;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 25);
        $notifications = NotificationModel::where('notifiable_type', \App\Models\Admin::class)
            ->where('notifiable_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('dashboard.admin.notifications.index', compact('notifications'));
    }

    public function toggleRead(Request $request, NotificationModel $notification)
    {
        if ($notification->notifiable_type !== \App\Models\Admin::class) {
            abort(403);
        }

        $notification->read_at = $notification->read_at ? null : now();
        $notification->save();

        $unreadCount = NotificationModel::where('notifiable_type', \App\Models\Admin::class)
            ->where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'notification' => $notification->id,
                'read' => (bool) $notification->read_at,
                'unreadCount' => $unreadCount,
            ]);
        }

        return back();
    }

    public function markAllRead(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || count($ids) === 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No ids provided']);
            }
            return back();
        }

        NotificationModel::whereIn('id', $ids)
            ->where('notifiable_type', \App\Models\Admin::class)
            ->update(['read_at' => now()]);
        $unreadCount = NotificationModel::where('notifiable_type', \App\Models\Admin::class)
            ->where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'unreadCount' => $unreadCount, 'marked' => $ids]);
        }

        return back();
    }
}
