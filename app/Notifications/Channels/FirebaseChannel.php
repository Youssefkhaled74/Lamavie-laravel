<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Log;

class FirebaseChannel
{
    protected $messaging;

    /**
     * Create a new channel instance.
     *
     * @param \Kreait\Firebase\Contract\Messaging $messaging
     */
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $token = $notifiable->routeNotificationForFirebase();
        if (!$token) {
            Log::warning('No FCM token found for notifiable', [
                'notifiable_id' => $notifiable->id,
                'notification' => get_class($notification),
            ]);
            return;
        }

        try {
            $message = $notification->toFirebase($notifiable);
            if ($message instanceof CloudMessage) {
                $this->messaging->send($message->withChangedTarget('token', $token));
                Log::info('Firebase notification sent successfully', [
                    'notifiable_id' => $notifiable->id,
                    'token' => $token,
                    'notification' => get_class($notification),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification', [
                'error' => $e->getMessage(),
                'notifiable_id' => $notifiable->id,
                'token' => $token,
                'notification' => get_class($notification),
            ]);
        }
    }
}