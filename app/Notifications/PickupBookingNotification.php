<?php

namespace App\Notifications;

class PickupBookingNotification extends BaseBookingNotification
{
    protected function getTitle($locale)
    {
        return $locale === 'ar' 
            ? 'تم تحديث حالة الحجز إلى جاري الاستلام' 
            : 'Booking Status Updated to Pickup';
    }

    protected function getBody($locale)
    {
        return $locale === 'ar'
            ? "حجزك رقم {$this->booking->order_number} قيد الاستلام. فريقنا في طريقه لاستلام طلبك."
            : "Your booking #{$this->booking->order_number} is being picked up. Our team is on the way to collect your order.";
    }
}