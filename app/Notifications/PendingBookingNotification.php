<?php

namespace App\Notifications;

class PendingBookingNotification extends BaseBookingNotification
{
    protected function getTitle($locale)
    {
        return $locale === 'ar' 
            ? 'تم تحديث حالة الحجز إلى قيد الانتظار' 
            : 'Booking Status Updated to Pending';
    }

    protected function getBody($locale)
    {
        return $locale === 'ar'
            ? "حجزك رقم {$this->booking->order_number} في حالة قيد الانتظار. سنتواصل معك قريبًا."
            : "Your booking #{$this->booking->order_number} is now pending. We will contact you soon.";
    }
}