<?php

namespace App\Notifications;

class LabArrivedNotification extends BaseBookingNotification
{
    protected function getTitle($locale)
    {
        return $locale === 'ar' ? 'وصل الطلب للمعمل' : 'Order arrived at lab';
    }

    protected function getBody($locale)
    {
        return $locale === 'ar'
            ? 'لقد وصل طلبك إلى المعمل.'
            : 'Your order has arrived at the lab.';
    }
}
