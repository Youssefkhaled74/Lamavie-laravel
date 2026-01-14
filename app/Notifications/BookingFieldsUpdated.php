<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Carbon\Carbon;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Illuminate\Support\Facades\Log;

class BookingFieldsUpdated extends Notification
{

    public $booking;
    public $changes;

    public function __construct($booking, array $changes)
    {
        $this->booking = $booking;
        $this->changes = $changes;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        try {
            if (!empty($notifiable->fcm_token)) {
                $channels[] = \App\Notifications\Channels\FirebaseChannel::class;
            }
        } catch (\Throwable $_) {}
        Log::info('BookingFieldsUpdated::via', ['notifiable_id' => $notifiable->id ?? null, 'channels' => $channels]);
        return $channels;
    }

    public function toFirebase($notifiable)
    {
        try {
            $title = 'Booking updated';
            $messages = $this->buildChangeMessages();
            $summary = implode('; ', array_slice($messages, 0, 3));
            $body = 'Your booking #' . ($this->booking->order_number ?? $this->booking->id) . ' was updated.' . ($summary ? ' Changes: ' . $summary : '');
            $msg = CloudMessage::new()
                ->withNotification(FirebaseNotification::create($title, $body))
                ->withData([
                    'booking_id' => (string)$this->booking->id,
                    'order_number' => $this->booking->order_number ?? null,
                    'changes' => json_encode($this->changes),
                    'friendly_messages' => json_encode($messages),
                ]);
            Log::info('BookingFieldsUpdated::toFirebase built', ['notifiable_id' => $notifiable->id ?? null]);
            return $msg;
        } catch (\Throwable $e) {
            Log::error('BookingFieldsUpdated::toFirebase failed', ['error' => $e->getMessage(), 'notifiable_id' => $notifiable->id ?? null]);
            return null;
        }
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'order_number' => $this->booking->order_number ?? null,
            'message' => 'Your booking was updated by admin.',
            'summary' => $this->buildChangeSummary(),
            'changes' => $this->changes,
            'friendly_messages' => $this->buildChangeMessages(),
        ];
    }

    /**
     * Build a short human-readable summary of the changes.
     * Example: "status: pending → pickup; total: 100.00 → 120.00"
     *
     * @return string
     */
    protected function buildChangeSummary(): string
    {
        if (empty($this->changes) || !is_array($this->changes)) return '';
        return implode('; ', $this->buildChangeMessages());
    }

    /**
     * Build an array of human friendly messages, one per changed field.
     * Example: ["Pickup date: 2025-01-01 → 2025-01-02", "Driver: — → Ahmed"]
     *
     * @return array
     */
    protected function buildChangeMessages(): array
    {
        if (empty($this->changes) || !is_array($this->changes)) return [];

        $messages = [];
        foreach ($this->changes as $field => $vals) {
            $old = array_key_exists('old', $vals) ? $vals['old'] : null;
            $new = array_key_exists('new', $vals) ? $vals['new'] : null;

            $label = $this->friendlyLabel($field);
            $oldStr = $this->formatFieldValue($field, $old);
            $newStr = $this->formatFieldValue($field, $new);

            $messages[] = $label . ': ' . $oldStr . ' → ' . $newStr;
        }

        return $messages;
    }

    protected function friendlyLabel(string $field): string
    {
        $map = [
            'status' => 'Status',
            'pickup_date' => 'Pickup date',
            'delivery_date' => 'Delivery date',
            'pickup_time' => 'Pickup time',
            'delivery_time' => 'Delivery time',
            'pickup_driver_id' => 'Pickup driver',
            'delivery_driver_id' => 'Delivery driver',
            'driver_id' => 'Driver',
            'lab_id' => 'Lab',
            'payment_method_id' => 'Payment method',
            'service_id' => 'Service',
            'total' => 'Total',
            'price' => 'Price',
            'notes' => 'Notes',
            'address' => 'Address',
            'phone' => 'Phone',
        ];
        return $map[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    protected function formatFieldValue(string $field, $value): string
    {
        if (is_null($value)) return '—';

        // Dates
        if (in_array($field, ['pickup_date', 'delivery_date'])) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Throwable $_) {
                return $this->stringify($value);
            }
        }

        // Times
        if (in_array($field, ['pickup_time','delivery_time'])) {
            try {
                return Carbon::parse($value)->format('H:i');
            } catch (\Throwable $_) {
                return $this->stringify($value);
            }
        }

        // Money-like
        if (in_array($field, ['total','price','amount'])) {
            if (is_numeric($value)) return number_format((float)$value, 2);
            return $this->stringify($value);
        }

        // IDs that map to models
        if (in_array($field, ['payment_method_id','lab_id','driver_id','pickup_driver_id','delivery_driver_id','service_id'])) {
            return $this->resolveFieldValue($field, $value);
        }

        // Notes/text — truncate
        if (in_array($field, ['notes','description'])) {
            $s = $this->stringify($value);
            return mb_strlen($s) > 120 ? mb_substr($s, 0, 117) . '...' : $s;
        }

        return $this->stringify($value);
    }

    /**
     * Try to convert certain field IDs into human readable names.
     */
    protected function resolveFieldValue(string $field, $value)
    {
        if (is_null($value)) return '—';

        // Payment method id -> name (en)
        if (in_array($field, ['payment_method_id'])) {
            try {
                $pm = \App\Models\PaymentMethod::find($value);
                if ($pm) {
                    $n = data_get($pm, 'name');
                    if (is_array($n)) return $n['en'] ?? (string)$pm->name;
                    return (string)$n;
                }
            } catch (\Throwable $_){}
        }

        // Lab id -> name
        if (in_array($field, ['lab_id'])) {
            try {
                $lab = \App\Models\Lab::find($value);
                if ($lab) return data_get($lab, 'name') ?? (string)$lab->id;
            } catch (\Throwable $_){}
        }

        // Driver ids -> driver name
        if (in_array($field, ['driver_id','pickup_driver_id','delivery_driver_id'])) {
            try {
                $drv = \App\Models\Driver::find($value);
                if ($drv) return $drv->name ?? ($drv->phone ?? (string)$drv->id);
            } catch (\Throwable $_){}
        }

        // Service id -> service name
        if ($field === 'service_id') {
            try {
                $svc = \App\Models\Service::find($value);
                if ($svc) {
                    $n = data_get($svc, 'name');
                    if (is_array($n)) return $n['en'] ?? (string)$svc->id;
                    return (string)$n;
                }
            } catch (\Throwable $_){}
        }

        // Fallback to stringify
        return $this->stringify($value);
    }

    protected function stringify($v)
    {
        if (is_null($v)) return '—';
        if (is_bool($v)) return $v ? 'true' : 'false';
        if (is_array($v)) return json_encode($v, JSON_UNESCAPED_UNICODE);
        return (string)$v;
    }
}
