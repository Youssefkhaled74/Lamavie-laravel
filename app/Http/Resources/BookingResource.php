<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Helper function to get locale-specific value
        $getLocalizedValue = function ($value) {
            $locale = App::getLocale();
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? reset($value) ?? $value;
            }
            return $value;
        };

        $photoUrl = null;
        if (isset($this->payload_data['photo'])) {
            $photoUrl = url('storage/' . ltrim($this->payload_data['photo'], '/'));
        }

        return [
            'booking_id' => $this->id,
            'booking_number' => $this->order_number,
            'user_id' => $this->user_id,
            'service_id' => $this->service_id,
            'service_name' => $this->service ? $getLocalizedValue($this->service->name) : null,
            'service_category_id' => $this->service_category_id,
            'service_category_name' => $this->serviceCategory ? $getLocalizedValue($this->serviceCategory->name) : null,
            'service_category_image' => $this->serviceCategory ? env('APP_URL') .'/storage/' . $this->serviceCategory->logo : null,
            'service_type_id' => $this->service_type_id,
            'service_type_name' => $this->serviceType ? $getLocalizedValue($this->serviceType->name) : null,
            'payload_data' => $this->payload_data,
            'photo_url' => $photoUrl,
            'payment_method' => $this->paymentMethod ? $this->paymentMethod->name[$request->header('Accept-Language', 'en')] ?? $this->paymentMethod->name['en'] : null,
            'status' => $this->status,
            'total' => (float) $this->total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}