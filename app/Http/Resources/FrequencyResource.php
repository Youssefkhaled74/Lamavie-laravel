<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class FrequencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Get the current locale, default to 'en' if not set or invalid
        $locale = App::getLocale() ?? 'en';
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        $getLocalizedValue = function ($value) {
            $locale = App::getLocale();
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? reset($value) ?? $value;
            }
            return $value;
        };

        $adjustPrice = function ($price) use ($request) {
            if ($price === null) return null;
            $percent = optional(optional($request->user())->area)->price_increase_percentage ?? 0;
            $final = (float) $price * (1 + ((float) $percent / 100));
            return $final !== null ? number_format($final, 2, '.', '') : null;
        };

        return [
            'id' => $this->id,
            'name' => $getLocalizedValue($this->name),
            'service_category_id' => $this->service_category_id,
            'price' => $adjustPrice($this->price),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}