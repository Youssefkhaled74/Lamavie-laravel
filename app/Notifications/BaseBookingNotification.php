<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;
use App\Models\Notification as NotificationModel;

abstract class BaseBookingNotification extends Notification
{
    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return $notifiable->fcm_token ? [\App\Channels\FirebaseChannel::class] : [];
    }

    abstract protected function getTitle($locale);
    abstract protected function getBody($locale);

    /**
     * Returns the click action URL used in the notification payload.
     * Child classes (e.g. admin notifications) may override this to point to admin routes.
     */
    protected function getClickAction($notifiable)
    {
        return url('/user/bookings/' . $this->booking->id);
    }

    public function toFirebase($notifiable)
    {
        Log::info('🔔 ' . static::class . '::toFirebase - Method started', [
            'booking_id' => $this->booking->id,
            'order_number' => $this->booking->order_number,
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'notifiable_email' => $notifiable->email ?? 'N/A',
            'notifiable_language' => $notifiable->language ?? 'N/A',
        ]);

        $locale = $notifiable->language ?? app()->getLocale();
        
        Log::info('🌍 ' . static::class . '::toFirebase - Locale determined', [
            'locale' => $locale,
            'notifiable_language' => $notifiable->language ?? 'default',
            'app_locale' => app()->getLocale(),
        ]);
        
        // Get titles and bodies for both languages
        $title_ar = $this->getTitle('ar');
        $title_en = $this->getTitle('en');
        $body_ar = $this->getBody('ar');
        $body_en = $this->getBody('en');
        
        Log::info('📝 ' . static::class . '::toFirebase - Titles and bodies generated', [
            'title_ar' => $title_ar,
            'title_en' => $title_en,
            'body_ar_length' => strlen($body_ar),
            'body_en_length' => strlen($body_en),
        ]);
        
        // Get localized content for the notification
        $localized_title = $locale === 'ar' ? $title_ar : $title_en;
        $localized_body = $locale === 'ar' ? $body_ar : $body_en;

        // Validate title and body
        if (!is_string($localized_title) || empty($localized_title)) {
            Log::error('Invalid title in ' . static::class, ['title' => $localized_title]);
            $localized_title = $locale === 'ar' ? $title_ar : $title_en;
        }
        if (!is_string($localized_body) || empty($localized_body)) {
            Log::error('Invalid body in ' . static::class, ['body' => $localized_body]);
            $localized_body = $locale === 'ar' ? $body_ar : $body_en;
        }

        $data = [
            'booking_id' => (string) $this->booking->id,
            'order_number' => $this->booking->order_number,
            'status' => $this->booking->status,
            'click_action' => $this->getClickAction($notifiable),
            'title' => [
                'ar' => $title_ar,
                'en' => $title_en,
            ],
            'body' => [
                'ar' => $body_ar,
                'en' => $body_en,
            ],
        ];

        // Save notification to database with JSON titles and bodies
        Log::info('💾 ' . static::class . '::toFirebase - Creating notification record', [
            'notifiable_id' => $notifiable->id,
            'booking_id' => $this->booking->id,
        ]);
        
        $notification = NotificationModel::create([
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable),
            'type' => static::class,
            'title' => [ // Store as JSON array - FIXED
                'ar' => $title_ar,
                'en' => $title_en,
            ],
            'body' => [ // Store as JSON array - FIXED
                'ar' => $body_ar,
                'en' => $body_en,
            ],
            'data' => $data, // Let the model handle JSON encoding
            'status' => 'pending',
        ]);

        Log::info('✅ ' . static::class . '::toFirebase - Notification record created', [
            'notification_id' => $notification->id,
            'status' => $notification->status,
        ]);

        // Debug: full notification model data
        try {
            Log::debug('💾 BaseBookingNotification - Notification model data', ['notification' => $notification->toArray()]);
        } catch (\Throwable $t) {
            Log::warning('⚠️ BaseBookingNotification - Failed to serialize notification model', ['err' => $t->getMessage()]);
        }

        try {
            Log::info('🔨 ' . static::class . '::toFirebase - Building CloudMessage', [
                'fcm_token_preview' => substr($notifiable->fcm_token, 0, 20) . '...',
                'title' => $localized_title,
                'body_length' => strlen($localized_body),
            ]);
            
            $msg = CloudMessage::new()
                ->withTarget('token', $notifiable->fcm_token)
                ->withNotification(FirebaseNotification::create($localized_title, $localized_body))
                ->withData([
                    'booking_id' => (string) $this->booking->id,
                    'order_number' => $this->booking->order_number,
                    'status' => $this->booking->status,
                    'click_action' => $this->getClickAction($notifiable),
                ]);
                
            Log::info('✅ ' . static::class . '::toFirebase - CloudMessage built successfully', [
                'has_notification' => true,
                'has_data' => true,
            ]);

            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info('✅ ' . static::class . '::toFirebase - Notification record updated to sent', [
                'notification_id' => $notification->id,
            ]);

            try {
                Log::debug('💾 BaseBookingNotification - Notification updated to sent', ['notification' => $notification->toArray()]);
            } catch (\Throwable $t) {
                Log::warning('⚠️ BaseBookingNotification - Failed to serialize notification after send', ['err' => $t->getMessage()]);
            }

            Log::info('🎉 ' . static::class . '::toFirebase - Method completed successfully', [
                'booking_id' => $this->booking->id,
                'notifiable_id' => $notifiable->id,
                'notification_id' => $notification->id,
            ]);

            return $msg;
        } catch (\Exception $e) {
            Log::error('❌ ' . static::class . '::toFirebase - Exception caught', [
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'booking_id' => $this->booking->id,
                'notifiable_id' => $notifiable->id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $notification->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            try {
                Log::debug('💾 BaseBookingNotification - Notification marked failed', ['notification' => $notification->toArray(), 'error' => $e->getMessage()]);
            } catch (\Throwable $t) {
                Log::warning('⚠️ BaseBookingNotification - Failed to serialize notification after failure', ['err' => $t->getMessage()]);
            }

            Log::error('💾 ' . static::class . '::toFirebase - Notification marked as failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
                'booking_id' => $this->booking->id,
                'notifiable_id' => $notifiable->id,
            ]);
            return null;
        }
    }
}