<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $appUrl = config('app.url'); // Use config instead of env

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile_photo' => $this->profile_photo ? $appUrl . '/storage/' . $this->profile_photo : null,
            'phone_verified_at' => $this->phone_verified_at ? $this->phone_verified_at->toDateTimeString() : null,
            'has_pending_phone_verification' => !is_null($this->new_phone), // Helper field
            'fcm_token' => $this->fcm_token,
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at : null,
            'otp_code' => $this->otp_code,
            'new_phone' => $this->new_phone,
            'new_phone_verified_at' => $this->new_phone_verified_at ? $this->new_phone_verified_at: null,
            'points' => $this->points,
            'area_id' => $this->area_id,
            'area' => $this->area ? [
                'id' => $this->area->id,
                'name' => $this->getLocalizedAreaName(),
                'slug' => $this->area->slug,
                'price_increase_percentage' => $this->area->price_increase_percentage,
            ] : null,
        ];
    }

    /**
     * Get localized area name based on current app locale
     */
    protected function getLocalizedAreaName()
    {
        if (!$this->area || !$this->area->name) {
            return null;
        }

        $locale = app()->getLocale();
        $names = is_array($this->area->name) ? $this->area->name : json_decode($this->area->name, true);

        return $names[$locale] ?? $names['en'] ?? null;
    }
}
