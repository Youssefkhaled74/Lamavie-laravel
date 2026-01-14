<?php

namespace App\Notifications;

class LabAssignedNotification extends BaseBookingNotification
{
    protected function getTitle($locale)
    {
        return $locale === 'ar' ? 'تم تحويل الطلب للمعمل' : 'Order assigned to lab';
    }

    protected function getBody($locale)
    {
        return $locale === 'ar'
            ? 'تم تحويل طلبك إلى المعمل وسيتم متابعته هناك.'
            : 'Your dry-clean order has been assigned to the lab.';
    }
}
