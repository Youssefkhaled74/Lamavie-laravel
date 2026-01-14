<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarWashDriver;
use App\Models\DriverVehicle;
use Illuminate\Support\Str;

class CarWashDriverSeeder extends Seeder
{
    public function run(): void
    {
        // Egyptian-style sample data
        $drivers = [
            ['name' => 'Ahmed Hossam', 'phone' => '+201001234567', 'email' => 'ahmed.hossam@example.com', 'license_number' => 'L-1001', 'notes' => 'Experienced driver'],
            ['name' => 'Mohamed Ali', 'phone' => '+201122334455', 'email' => 'mohamed.ali@example.com', 'license_number' => 'L-1002', 'notes' => 'Available mornings'],
            ['name' => 'Karim Hussein', 'phone' => '+201155667788', 'email' => 'karim.hussein@example.com', 'license_number' => 'L-1003', 'notes' => 'Prefers city routes'],
            ['name' => 'Youssef Salah', 'phone' => '+201188990011', 'email' => 'youssef.salah@example.com', 'license_number' => 'L-1004', 'notes' => 'Speaks English']
        ];

        $vehicles = [
            ['plate_number' => 'EGP-1234', 'make' => 'Toyota', 'model' => 'Corolla', 'color' => 'White', 'capacity' => 4],
            ['plate_number' => 'EGP-5678', 'make' => 'Hyundai', 'model' => 'Elantra', 'color' => 'Silver', 'capacity' => 4],
            ['plate_number' => 'EGP-9012', 'make' => 'Nissan', 'model' => 'Sunny', 'color' => 'Black', 'capacity' => 4],
            ['plate_number' => 'EGP-3456', 'make' => 'KIA', 'model' => 'Cerato', 'color' => 'Blue', 'capacity' => 4],
        ];

        // Create drivers and give each one vehicle (attach via pivot)
        foreach ($drivers as $i => $d) {
            $driver = CarWashDriver::create($d);

            $vehData = $vehicles[$i] ?? $vehicles[array_rand($vehicles)];
            $veh = DriverVehicle::create($vehData);
            $driver->vehicles()->attach($veh->id);
        }
    }
}
