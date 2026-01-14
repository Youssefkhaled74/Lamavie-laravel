<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Resources\Json\JsonResource;

class AreaResource extends JsonResource
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
            'slug' => $this->slug,
            'description' => $this->description,
            'price_increase_percentage' => $this->price_increase_percentage,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
