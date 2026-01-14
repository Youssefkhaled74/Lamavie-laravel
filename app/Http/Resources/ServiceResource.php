<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Set locale based on Accept-Language header
        $locale = $request->header('Accept-Language', 'en');
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en'; // Fallback to English
        }
        App::setLocale($locale);

        // Helper function to get locale-specific value or raw string
        $getLocalizedValue = function ($value) use ($locale) {
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? $value; // Fallback to any available key or raw value
            }
            return $value; // Return raw string if not an array
        };

        return [
            'id' => $this->id,
            'name' => $getLocalizedValue($this->name),
            'logo' => $this->logo ? asset('storage/' . $this->logo) : null,
            'about' => $getLocalizedValue($this->about),
            'description' => $getLocalizedValue($this->description),
            'service_types' => ServiceTypeResource::collection($this->whenLoaded('serviceTypes')),
            'photos' => PhotoServiceResource::collection($this->whenLoaded('photoServices')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}