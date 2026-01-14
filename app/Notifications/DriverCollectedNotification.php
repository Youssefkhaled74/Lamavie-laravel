<?php

namespace App\Notifications;

class DriverCollectedNotification extends BaseBookingNotification
{
    protected function getTitle($locale)
    {
        return $locale === 'ar' ? 'استلم السائق العناصر' : 'Driver collected items';
    }

    protected function getBody($locale)
    {
        return $locale === 'ar'
            ? 'استلم السائق العناصر منك وسيتم توصيلها إلى المعمل.'
            : 'The driver has collected the items from you and will deliver them to the lab.';
    }
}
