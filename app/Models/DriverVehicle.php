<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CarWashDriver;

class DriverVehicle extends Model
{
    use HasFactory;

    protected $table = 'driver_vehicles';

    protected $fillable = ['plate_number','make','model','color','capacity'];

    // A vehicle may belong to many drivers (many-to-many)
    public function drivers()
    {
        return $this->belongsToMany(CarWashDriver::class, 'car_wash_driver_driver_vehicle', 'driver_vehicle_id', 'car_wash_driver_id')->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(\App\Models\BookingCarAssignment::class, 'driver_vehicle_id');
    }
}
