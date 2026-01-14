<?php

namespace App\Notifications;

class LabPickedNotification extends BaseBookingNotification
{
    protected function getTitle($locale)
    {
        return $locale === 'ar' ? 'استُلم الطلب من المعمل' : 'Order picked up from lab';
    }

    protected function getBody($locale)
    {
        return $locale === 'ar'
            ? 'تم استلام طلبك من المعمل وسيتم توصيله إليك.'
            : 'Your order has been picked up from the lab and will be delivered.';
    }
}
