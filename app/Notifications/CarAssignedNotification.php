<?php

namespace App\Notifications;

use App\Notifications\BaseBookingNotification;
use Carbon\Carbon;

class CarAssignedNotification extends BaseBookingNotification
{
    protected $assignment;

    public function __construct($booking, $assignment)
    {
        parent::__construct($booking);
        $this->assignment = $assignment;
    }

    protected function getTitle($locale)
    {
        if ($locale === 'ar') return 'تم تعيين سيارة لطلبك';
        return 'Car assigned to your order';
    }

    protected function getBody($locale)
    {
        $start = $this->assignment->start_at ? Carbon::parse($this->assignment->start_at)->format('d M Y, H:i') : null;
        $end = $this->assignment->end_at ? Carbon::parse($this->assignment->end_at)->format('d M Y, H:i') : null;

        if ($locale === 'ar') {
            if ($start && $end) return "ستكون السيارة متجهة إليك في الفترة من {$start} إلى {$end}.";
            if ($start) return "من المتوقع أن تصل السيارة في {$start}.";
            return 'تم تعيين السيارة لطلبك. سيصلك موعد الوصول قريباً.';
        }

        if ($start && $end) return "The car is scheduled to arrive between {$start} and {$end}.";
        if ($start) return "The car is scheduled to arrive at {$start}.";
        return 'A car has been assigned to your booking. Arrival time will be provided soon.';
    }
}
