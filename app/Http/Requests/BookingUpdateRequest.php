<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->guard('admin')->check();
    }

    public function rules()
    {
        $rules = [
            'status' => 'nullable|in:pending,pickup,delivered,canceled',
            'driver_id' => 'nullable|exists:drivers,id',
            'pickup_driver_id' => 'nullable|exists:drivers,id',
            'delivery_driver_id' => 'nullable|exists:drivers,id',
            'lab_id' => 'nullable|exists:labs,id',
            'pickup_date' => 'nullable|date',
            'pickup_time' => 'nullable|string',
            'pickup_location' => 'nullable|string',
            'delivery_location' => 'nullable|string',
            'total' => 'nullable|numeric|between:0,99999999.99',
            'payment_method_id' => 'nullable|integer',
            'notes' => 'nullable|string',
        ];
        return $rules;
    }

    public function validatedFiltered()
    {
        $data = $this->validated();
        // Remove nulls so controller logic doesn't accidentally overwrite
        return array_filter($data, fn($v) => $v !== null);
    }
}
