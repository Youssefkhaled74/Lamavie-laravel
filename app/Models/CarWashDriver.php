<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DriverVehicle;

class CarWashDriver extends Model
{
    use HasFactory;

    protected $table = 'car_wash_drivers';

    protected $fillable = ['name','phone','email','license_number','notes'];

    // A driver may belong to many vehicles (many-to-many). Use pivot table car_wash_driver_driver_vehicle.
    public function vehicles()
    {
        return $this->belongsToMany(DriverVehicle::class, 'car_wash_driver_driver_vehicle', 'car_wash_driver_id', 'driver_vehicle_id')->withTimestamps();
    }

}
