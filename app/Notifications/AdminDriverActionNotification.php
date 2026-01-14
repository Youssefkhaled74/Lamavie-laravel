<?php

namespace App\Notifications;

class AdminDriverActionNotification extends BaseBookingNotification
{
    protected $driverName;
    protected $action;

    public function __construct($booking, $driverName, $action)
    {
        parent::__construct($booking);
        $this->driverName = $driverName;
        $this->action = $action; // short action string like 'arrived_at_lab', 'picked_from_lab', 'status_changed'
    }

    protected function getTitle($locale)
    {
        $order = $this->booking->order_number ?? $this->booking->id;
        switch ($this->action) {
            case 'arrived_at_lab':
                return $locale === 'ar' ? "سائق وصل للمعمل (حجز #{$order})" : "Driver arrived at lab (Booking #{$order})";
            case 'picked_from_lab':
                return $locale === 'ar' ? "استلم السائق من المعمل (حجز #{$order})" : "Driver picked from lab (Booking #{$order})";
            case 'driver_collected':
                return $locale === 'ar' ? "تم التحصيل من العميل (حجز #{$order})" : "Driver collected from user (Booking #{$order})";
            case 'status_changed':
            default:
                return $locale === 'ar' ? "تحديث حالة من السائق (حجز #{$order})" : "Driver updated booking (Booking #{$order})";
        }
    }

    protected function getBody($locale)
    {
        $driver = $this->driverName ?: 'Driver';
        switch ($this->action) {
            case 'arrived_at_lab':
                return $locale === 'ar' ? "{$driver} وصل إلى المعمل للحجز رقم {$this->booking->order_number}." : "{$driver} arrived at the lab for booking {$this->booking->order_number}.";
            case 'picked_from_lab':
                return $locale === 'ar' ? "{$driver} استلم الملابس من المعمل للحجز رقم {$this->booking->order_number}." : "{$driver} picked items from the lab for booking {$this->booking->order_number}.";
            case 'driver_collected':
                return $locale === 'ar' ? "{$driver} استلم المبلغ من العميل للحجز رقم {$this->booking->order_number}." : "{$driver} collected payment from user for booking {$this->booking->order_number}.";
            case 'status_changed':
            default:
                return $locale === 'ar' ? "{$driver} قام بتحديث حالة الحجز رقم {$this->booking->order_number}." : "{$driver} updated the booking status for booking {$this->booking->order_number}.";
        }
    }

    protected function getClickAction($notifiable)
    {
        try {
            return route('admin.bookings.show', $this->booking->id);
        } catch (\Exception $e) {
            return url('/admin/bookings/' . $this->booking->id);
        }
    }
}
