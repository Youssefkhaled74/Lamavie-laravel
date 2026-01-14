<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = ['name', 'logo', 'about', 'description'];

    protected $casts = [
        'name' => 'array',
        'about' => 'array',
        'description' => 'array',
    ];

    public function serviceTypes()
    {
        return $this->hasMany(ServiceType::class);
    }

    public function photoServices()
    {
        return $this->hasMany(PhotoService::class);
    }

    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'driver_service')->withTimestamps();
    }

    // Accessors for multilingual fields
    public function getNameAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getAboutAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getDescriptionAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    // Mutators for multilingual fields
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = json_encode($value);
    }

    public function setAboutAttribute($value)
    {
        $this->attributes['about'] = json_encode($value);
    }

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = json_encode($value);
    }
}
