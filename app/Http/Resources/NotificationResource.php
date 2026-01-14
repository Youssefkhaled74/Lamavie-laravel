<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // Get the current locale from the request or app locale
        $locale = $request->header('Accept-Language', app()->getLocale());
        
        // Ensure valid locale
        $locale = in_array($locale, ['ar', 'en']) ? $locale : 'en';

        // Helper function to get localized value
        $getLocalizedValue = function ($value, $default = '') use ($locale) {
            if (is_array($value)) {
                return $value[$locale] ?? $value['en'] ?? $value['ar'] ?? $default;
            }
            return $value ?: $default;
        };

        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $getLocalizedValue($this->title, $this->data['title'][$locale] ?? ''),
            'body' => $getLocalizedValue($this->body, $this->data['body'][$locale] ?? ''),
            'data' => $this->data,
            'status' => $this->status,
            'sent_at' => $this->sent_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}