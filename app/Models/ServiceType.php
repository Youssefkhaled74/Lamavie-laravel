<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $table = 'service_types';

    protected $fillable = ['name', 'service_id', 'logo'];

    protected $casts = [
        'name' => 'array', // Cast the name field to an array
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}