<?php

namespace App\Notifications;

class AdminNewBookingNotification extends BaseBookingNotification
{

    protected function getTitle($locale)
    {
        return $locale === 'ar'
            ? "حجز جديد رقم {$this->booking->order_number}"
            : "New Booking #{$this->booking->order_number}";
    }

    protected function getBody($locale)
    {
        $userName = $this->booking->user->name ?? ($this->booking->user_name ?? '—');
        return $locale === 'ar'
            ? "تم إنشاء حجز جديد من {$userName}. رقم الطلب: {$this->booking->order_number}"
            : "A new booking has been created by {$userName}. Order #: {$this->booking->order_number}";
    }

    /**
     * Point admins to the admin booking show page.
     */
    protected function getClickAction($notifiable)
    {
        try {
            return route('admin.bookings.show', $this->booking->id);
        } catch (\Exception $e) {
            return url('/admin/bookings/' . $this->booking->id);
        }
    }
}
