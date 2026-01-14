<?php

namespace App\Notifications;

use App\Notifications\BaseBookingNotification;

class DriverAssignedNotification extends BaseBookingNotification
{
    protected function getTitle($locale)
    {
        if ($locale === 'ar') return 'تم تعيين سائق لطلبك';
        return 'Driver assigned to your order';
    }

    protected function getBody($locale)
    {
        $driverName = $this->booking->driver->name ?? null;
        if ($locale === 'ar') {
            if ($driverName) return "تم تعيين السائق {$driverName} لاستلام طلبك.";
            return 'تم تعيين سائق لاستلام طلبك.';
        }
        if ($driverName) return "Your order has been assigned to driver {$driverName}.";
        return 'Your order has been assigned to a driver.';
    }
}
