<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class CarpetMaterialResource extends JsonResource
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
            'service_category_id' => $this->service_category_id,
            'service_category_name' => $this->serviceCategory ? $getLocalizedValue($this->serviceCategory->name) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}