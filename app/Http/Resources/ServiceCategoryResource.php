<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCategoryResource extends JsonResource
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

        return [
            'id' => $this->id,
            'name' => $getLocalizedValue($this->name),
            // 'logo' => $this->logo,
            'logo'=> $this->logo ? env('APP_URL'). Storage::url($this->logo) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}