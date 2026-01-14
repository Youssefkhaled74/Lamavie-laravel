<?php

namespace App\Notifications;

class DriverReturnedNotification extends BaseBookingNotification
{
    protected function getTitle($locale)
    {
        return $locale === 'ar' ? 'تم التسليم للعميل' : 'Returned to customer';
    }

    protected function getBody($locale)
    {
        return $locale === 'ar'
            ? 'تم تسليم العناصر إلى العميل بواسطة السائق.'
            : 'The items have been returned to the customer by the driver.';
    }
}
