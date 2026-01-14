<?php

namespace App\Notifications;

class DeliveredBookingNotification extends BaseBookingNotification
{
    protected function getTitle($locale)
    {
        return $locale === 'ar' 
            ? 'تم تحديث حالة الحجز إلى تم التسليم' 
            : 'Booking Status Updated to Delivered';
    }

    protected function getBody($locale)
    {
        return $locale === 'ar'
            ? "حجزك رقم {$this->booking->order_number} تم تسليمه بنجاح. شكرًا لثقتك بنا!"
            : "Your booking #{$this->booking->order_number} has been successfully delivered. Thank you for choosing us!";
    }
}