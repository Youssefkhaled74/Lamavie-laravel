<?php

namespace App\Http\Controllers\Dashboard\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationApiController extends Controller
{
    /**
     * Return unseen bookings count and latest unseen booking details.
     */
    public function unseen(Request $request)
    {
        $admin = auth('admin')->user();
        if (! $admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // If there's a short-lived broadcast in cache, return it so dashboard shows immediate alert
        $broadcast = Cache::get('admin.notifications.broadcast');
        if ($broadcast) {
            // return broadcast payload and maintain unseen_count if provided
            return response()->json([
                'status' => 'success',
                'data' => [
                    'unseen_count' => $broadcast['unseen_count'] ?? 1,
                    'broadcast' => $broadcast,
                ],
            ], 200);
        }

        $count = Booking::where('is_unseen', true)->count();

        $latest = Booking::where('is_unseen', true)
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'unseen_count' => $count,
                'latest' => $latest ? [
                    'id' => $latest->id,
                    'order_number' => $latest->order_number,
                    'created_at' => $latest->created_at ? $latest->created_at->toIso8601String() : null,
                    'user_name' => $latest->user->name ?? ($latest->user_name ?? null),
                ] : null,
            ],
        ], 200);
    }

    /**
     * Emit a short-lived broadcast payload so dashboard clients show an alert.
     * Does NOT use queues or Firebase. Payload stored in cache for short TTL (default 30s).
     *
     * Body: { title?: string, body?: string, order_number?: string, unseen_count?: int }
     */
    public function emit(Request $request)
    {
        $admin = auth('admin')->user();
        if (! $admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $payload = $request->only(['title', 'body', 'order_number', 'unseen_count']);
        $payload = array_filter($payload, function ($v) { return $v !== null && $v !== ''; });

        if (empty($payload)) {
            return response()->json(['error' => 'Empty payload'], 422);
        }

        // Store in cache for short time so polling dashboard clients will pick it up
        Cache::put('admin.notifications.broadcast', $payload, now()->addSeconds(30));
        Log::info('NotificationApiController::emit - Broadcast stored in cache', ['payload' => $payload, 'by_admin' => $admin->id]);

        return response()->json(['status' => 'success', 'message' => 'Broadcast stored'], 200);
    }

    /**
     * Mark bookings as seen.
     * If `booking_id` is provided in the body, mark that booking as seen; otherwise mark all unseen bookings as seen.
     */
    public function markSeen(Request $request)
    {
        $admin = auth('admin')->user();
        if (! $admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $bookingId = $request->input('booking_id');
        if ($bookingId) {
            $b = Booking::find($bookingId);
            if (! $b) {
                return response()->json(['status' => 'error', 'message' => 'Booking not found'], 404);
            }
            $b->is_unseen = false;
            $b->save();
            // Clear any short-lived broadcast in cache to avoid duplicate alerts
            \Illuminate\Support\Facades\Cache::forget('admin.notifications.broadcast');
            return response()->json(['status' => 'success', 'marked' => 1], 200);
        }

        $marked = Booking::where('is_unseen', true)->update(['is_unseen' => false]);
        \Illuminate\Support\Facades\Cache::forget('admin.notifications.broadcast');
        return response()->json(['status' => 'success', 'marked' => $marked], 200);
    }
}
