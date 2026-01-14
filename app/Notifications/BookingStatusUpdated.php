<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class BookingStatusUpdated extends Notification
{

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    /**
     * Delivery channels.
     */
    public function via(object $notifiable): array
    {
        return [\App\Channels\FirebaseChannel::class];
    }

    /**
     * Build the Firebase message.
     */
    public function toFirebase($notifiable)
    {
        $fcmToken = $notifiable->fcm_token ?? null;
        if (!$fcmToken) {
            return null;
        }

        // Detect language
        $language = $notifiable->language ?? 'en';

        // Messages by status
        $statusMessages = [
            'pending' => [
                'en' => "Your booking #{$this->booking->order_number} is pending.",
                'ar' => "حجزك رقم #{$this->booking->order_number} في انتظار التأكيد.",
            ],
            'pickup' => [
                'en' => "Your booking #{$this->booking->order_number} is scheduled for pickup.",
                'ar' => "حجزك رقم #{$this->booking->order_number} تم تحديده للاستلام.",
            ],
            'delivered' => [
                'en' => "Your booking #{$this->booking->order_number} has been delivered.",
                'ar' => "حجزك رقم #{$this->booking->order_number} تم تسليمه.",
            ],
            'canceled' => [
                'en' => "Your booking #{$this->booking->order_number} has been canceled.",
                'ar' => "حجزك رقم #{$this->booking->order_number} تم إلغاؤه.",
            ],
        ];

        // Get message content
        $title = $language === 'ar' ? 'تحديث حالة الحجز' : 'Booking Status Update';
        $body = $statusMessages[$this->booking->status][$language] 
            ?? $statusMessages[$this->booking->status]['en'];

        // Build Notification
        $notification = FirebaseNotification::create($title, $body);

        // Build CloudMessage
        return CloudMessage::withTarget('token', $fcmToken)
            ->withNotification($notification)
            ->withData([
                'booking_id'   => (string) $this->booking->id,
                'order_number' => $this->booking->order_number,
                'status'       => $this->booking->status,
                'click_action' => route('user.bookings.show', $this->booking->id),
            ]);
    }
}
