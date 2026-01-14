<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'service_id',
        'service_category_id',
        'service_type_id',
        'driver_id',
        'pickup_driver_id',
        'delivery_driver_id',
        'payload_data',
        'status',
        'order_number',
        'total',
        'payment_method_id',
        'lab_id',
        'lab_assigned_at',
        'lab_arrived_at',
        'lab_picked_at',
        'driver_collected_at',
        'driver_returned_at',
        'is_unseen',
    ];

    protected $casts = [
        'payload_data' => 'array',
        'total' => 'decimal:2',
        'driver_id' => 'integer',
        'pickup_driver_id' => 'integer',
        'delivery_driver_id' => 'integer',
        'is_unseen' => 'boolean',
        'lab_assigned_at' => 'datetime',
        'lab_arrived_at' => 'datetime',
        'lab_picked_at' => 'datetime',
        'driver_collected_at' => 'datetime',
        'driver_returned_at' => 'datetime',
    ];

    // Convenience attribute to check if booking was returned to user
    public function getIsReturnedAttribute()
    {
        return !empty($this->driver_returned_at);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function pickupDriver()
    {
        return $this->belongsTo(Driver::class, 'pickup_driver_id');
    }

    public function deliveryDriver()
    {
        return $this->belongsTo(Driver::class, 'delivery_driver_id');
    }

    // Compatibility accessors: if DB columns for pickup/delivery driver do not exist yet,
    // read/write values from `payload_data` so the admin edit form still works.
    public function getPickupDriverIdAttribute($value)
    {
        if (!is_null($value)) return $value;
        $payload = $this->payload_data ?? [];
        if (isset($payload['pickup_driver_id'])) return $payload['pickup_driver_id'];
        // fallback to legacy driver_id if present
        return $this->attributes['driver_id'] ?? null;
    }

    public function setPickupDriverIdAttribute($value)
    {
        // If bookings table has the column, Eloquent will persist via attributes; otherwise store in payload_data
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn($this->getTable(), 'pickup_driver_id')) {
                $this->attributes['pickup_driver_id'] = $value;
                return;
            }
        } catch (\Throwable $e) {
            // ignore schema checks in environments where DB isn't available at runtime
        }

        $payload = $this->payload_data ?? [];
        $payload['pickup_driver_id'] = $value;
        $this->payload_data = $payload;
        $this->attributes['payload_data'] = is_array($payload) ? json_encode($payload) : $payload;
    }

    public function getDeliveryDriverIdAttribute($value)
    {
        if (!is_null($value)) return $value;
        $payload = $this->payload_data ?? [];
        return $payload['delivery_driver_id'] ?? null;
    }

    public function setDeliveryDriverIdAttribute($value)
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn($this->getTable(), 'delivery_driver_id')) {
                $this->attributes['delivery_driver_id'] = $value;
                return;
            }
        } catch (\Throwable $e) {
        }

        $payload = $this->payload_data ?? [];
        $payload['delivery_driver_id'] = $value;
        $this->payload_data = $payload;
        $this->attributes['payload_data'] = is_array($payload) ? json_encode($payload) : $payload;
    }
}