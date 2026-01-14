<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;

class AdminCustomMessage extends Notification
{
    public $booking;
    public $message;
    public $title;

    public function __construct($booking, string $message, string $title = null)
    {
        $this->booking = $booking;
        $this->message = $message;
        $this->title = $title ?? 'Message from admin';
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        try {
            if (!empty($notifiable->fcm_token)) {
                $channels[] = \App\Notifications\Channels\FirebaseChannel::class;
            }
        } catch (\Throwable $_) {}
        Log::info('AdminCustomMessage::via', ['notifiable_id' => $notifiable->id ?? null, 'channels' => $channels]);
        return $channels;
    }

    public function toFirebase($notifiable)
    {
        try {
            $title = $this->title;
            $body = $this->message;
            $msg = CloudMessage::new()
                ->withNotification(FirebaseNotification::create($title, $body))
                ->withData([
                    'booking_id' => (string)($this->booking->id ?? ''),
                    'order_number' => $this->booking->order_number ?? null,
                    'custom_message' => $this->message,
                ]);
            Log::info('AdminCustomMessage::toFirebase built', ['booking_id' => $this->booking->id ?? null]);
            return $msg;
        } catch (\Throwable $e) {
            Log::error('AdminCustomMessage::toFirebase failed', ['error' => $e->getMessage(), 'booking_id' => $this->booking->id ?? null]);
            return null;
        }
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id ?? null,
            'order_number' => $this->booking->order_number ?? null,
            'message' => $this->message,
            'title' => $this->title,
        ];
    }
}
