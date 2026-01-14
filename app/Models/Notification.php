<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Notification extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'notifiable_id',
        'notifiable_type',
        'type',
        'title',
        'body',
        'data',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->getKey())) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // Set mutators to ensure proper JSON encoding for title and body
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = is_array($value) 
            ? json_encode($value, JSON_UNESCAPED_UNICODE) 
            : json_encode(['ar' => $value, 'en' => $value], JSON_UNESCAPED_UNICODE);
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = is_array($value) 
            ? json_encode($value, JSON_UNESCAPED_UNICODE) 
            : json_encode(['ar' => $value, 'en' => $value], JSON_UNESCAPED_UNICODE);
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = is_array($value) 
            ? json_encode($value, JSON_UNESCAPED_UNICODE) 
            : $value;
    }

    // Accessors for proper JSON decoding
    public function getTitleAttribute($value)
    {
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : ['ar' => $value, 'en' => $value];
    }

    public function getBodyAttribute($value)
    {
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : ['ar' => $value, 'en' => $value];
    }

    // Helper methods to get localized content
    public function getLocalizedTitle($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $title = $this->getTitleAttribute($this->attributes['title'] ?? '');
        return $title[$locale] ?? $title['en'] ?? $title['ar'] ?? null;
    }

    public function getLocalizedBody($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $body = $this->getBodyAttribute($this->attributes['body'] ?? '');
        return $body[$locale] ?? $body['en'] ?? $body['ar'] ?? null;
    }

    // Your existing accessors (keep them for backward compatibility)
    public function getTitleArAttribute()
    {
        $title = $this->getTitleAttribute($this->attributes['title'] ?? '');
        return $title['ar'] ?? null;
    }

    public function getTitleEnAttribute()
    {
        $title = $this->getTitleAttribute($this->attributes['title'] ?? '');
        return $title['en'] ?? null;
    }

    public function getBodyArAttribute()
    {
        $body = $this->getBodyAttribute($this->attributes['body'] ?? '');
        return $body['ar'] ?? null;
    }

    public function getBodyEnAttribute()
    {
        $body = $this->getBodyAttribute($this->attributes['body'] ?? '');
        return $body['en'] ?? null;
    }
}