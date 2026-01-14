<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Retrieve notifications for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications(Request $request)
    {
        // Authenticate the user using the Sanctum token
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated. Invalid or missing token.',
            ], 401);
        }

        // Get pagination parameters from request with defaults
        $perPage = $request->get('per_page', 15);
        $perPage = max(1, min(100, (int)$perPage));

        // Query notifications for the authenticated user with pagination
        $notifications = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Return paginated response with custom structure
        Log::info('📥 NotificationController::getNotifications - returning notifications', [
            'user_id' => $user->id,
            'count' => $notifications->total(),
            'first_item_id' => $notifications->first()?->id,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => NotificationResource::collection($notifications),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'last_page' => $notifications->lastPage(),
            ],
        ], 200);
    }
}