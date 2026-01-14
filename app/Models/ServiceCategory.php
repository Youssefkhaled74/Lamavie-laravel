<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $table = 'service_categories';

    protected $fillable = ['name', 'service_id', 'logo'];
    
    protected $casts = [
        'name' => 'array', // Cast the name field to an array
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function maintenanceOrCleaning()
    {
        return $this->hasMany(MaintenanceOrCleaning::class);
    }

    public function carpetMaterial()
    {
        return $this->hasMany(CarpetMaterial::class);
    }

    public function typeOfStain()
    {
        return $this->hasMany(TypeOfStain::class);
    }

    public function sizeOfStain()
    {
        return $this->hasMany(SizeOfStain::class);
    }

    public function carpetSize()
    {
        return $this->hasMany(CarpetSize::class);
    }

    public function yourItems()
    {
        return $this->hasMany(YourItems::class);
    }
}
