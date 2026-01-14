<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService
{
    /**
     * Safely notify a notifiable. Will log any exceptions and return boolean success.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @param  array  $context
     * @return bool
     */
    public static function safeNotify($notifiable, $notification, array $context = []): bool
    {
        if (!$notifiable) return false;
        try {
            // Inspect channels for better logging/debugging
            try {
                $channels = method_exists($notification, 'via') ? $notification->via($notifiable) : [];
            } catch (\Throwable $e) {
                $channels = [];
            }

            $token = null;
            try { $token = $notifiable->fcm_token ?? null; } catch (\Throwable $_) { $token = null; }

            Log::info('Notification dispatch requested', array_merge([
                'notification' => get_class($notification),
                'channels' => $channels,
                'notifiable_id' => $notifiable->id ?? null,
                'has_fcm_token' => (bool)$token,
            ], $context));

            // Use Laravel's Notification facade to send immediately (no queue)
            NotificationFacade::sendNow($notifiable, $notification);
            Log::info('Notification sent (sync)', array_merge(['notification' => get_class($notification)], $context));
            return true;
        } catch (\Throwable $e) {
            Log::error('Notification failed (sync)', array_merge([
                'error' => $e->getMessage(),
                'notification' => get_class($notification),
            ], $context));
            return false;
        }
    }
}
