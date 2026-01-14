<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class CanceledBookingNotification extends BaseBookingNotification
{
    protected function getTitle($locale)
    {
        return $locale === 'ar' ? 'تم إلغاء الحجز' : 'Booking Canceled';
    }

    protected function getBody($locale)
    {
        return $this->getNotificationMessage($locale);
    }

    protected function getNotificationMessage($locale)
    {
        try {
            $setting = Setting::where('key', 'notification_canceled')->first();

            if ($setting && $setting->value) {
                $messageData = json_decode($setting->value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($messageData)) {
                    $message = $messageData[$locale] ?? $messageData['en'] ?? null;
                    if ($message) {
                        return str_replace('{order_number}', $this->booking->order_number, $message);
                    }
                }
            }

            return $locale === 'ar'
                ? "حجزك رقم {$this->booking->order_number} تم إلغاؤه. يرجى التواصل معنا لمزيد من التفاصيل."
                : "Your booking #{$this->booking->order_number} has been canceled. Please contact us for more details.";
        } catch (\Exception $e) {
            Log::error('CanceledBookingNotification: Failed to retrieve notification message from settings', [
                'error' => $e->getMessage(),
                'booking_id' => $this->booking->id,
            ]);

            return $locale === 'ar'
                ? "حجزك رقم {$this->booking->order_number} تم إلغاؤه. يرجى التواصل معنا لمزيد من التفاصيل."
                : "Your booking #{$this->booking->order_number} has been canceled. Please contact us for more details.";
        }
    }
}