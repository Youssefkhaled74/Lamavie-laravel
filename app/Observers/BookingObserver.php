<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\Admin;
use App\Notifications\AdminNewBookingNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        Log::info('🔔 BookingObserver::created - Event triggered', [
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'service_id' => $booking->service_id,
            'order_number' => $booking->order_number,
            'status' => $booking->status,
            'total' => $booking->total,
        ]);
        
        try {
            // Notify all admins that have an fcm_token
            Log::info('🔍 BookingObserver::created - Querying admins', [
                'booking_id' => $booking->id,
            ]);
            
            $admins = Admin::whereNotNull('fcm_token')->get();
            
            Log::info('👥 BookingObserver::created - Admins query result', [
                'booking_id' => $booking->id,
                'total_admins' => Admin::count(),
                'admins_with_tokens' => $admins->count(),
                'admin_ids' => $admins->pluck('id')->toArray(),
                'admin_emails' => $admins->pluck('email')->toArray(),
                'fcm_token_previews' => $admins->map(function($admin) {
                    return [
                        'id' => $admin->id,
                        'email' => $admin->email,
                        'token_preview' => substr($admin->fcm_token, 0, 20) . '...',
                        'token_length' => strlen($admin->fcm_token),
                    ];
                })->toArray(),
            ]);
            
            if ($admins->isEmpty()) {
                Log::warning('⚠️ BookingObserver::created - No admins with FCM tokens', [
                    'booking_id' => $booking->id,
                    'total_admins_in_db' => Admin::count(),
                ]);
                return;
            }
            
            // Create notification instance
            Log::info('📝 BookingObserver::created - Creating notification instance', [
                'booking_id' => $booking->id,
                'recipients_count' => $admins->count(),
            ]);
            
            $notification = new AdminNewBookingNotification($booking);
            
            Log::info('✅ BookingObserver::created - Notification instance created', [
                'notification_class' => get_class($notification),
                'booking_id' => $booking->id,
            ]);
            
            // Send notifications
            Log::info('📤 BookingObserver::created - Calling Notification::send()', [
                'booking_id' => $booking->id,
                'recipients_count' => $admins->count(),
                'queue_driver' => config('queue.default'),
            ]);
            Notification::send($admins, $notification);
            
            Log::info('✅ BookingObserver::created - Notification::send() completed', [
                'booking_id' => $booking->id,
                'recipients_count' => $admins->count(),
                'queue_driver' => config('queue.default'),
                'notification_class' => get_class($notification),
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ BookingObserver::created - Exception caught', [
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
                'booking_id' => $booking->id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
        
        Log::info('🏁 BookingObserver::created - Method completed', [
            'booking_id' => $booking->id,
        ]);
    }
}
