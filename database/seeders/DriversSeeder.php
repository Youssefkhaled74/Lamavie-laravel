<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\Service;
use Illuminate\Support\Str;

class DriversSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            ['name' => 'Ahmed Hossam', 'email' => 'ahmed.hossam@example.com', 'phone' => '+201001234567'],
            ['name' => 'Mohamed Ali', 'email' => 'mohamed.ali@example.com', 'phone' => '+201122334455'],
            ['name' => 'Karim Hussein', 'email' => 'karim.hussein@example.com', 'phone' => '+201155667788'],
            ['name' => 'Youssef Salah', 'email' => 'youssef.salah@example.com', 'phone' => '+201188990011'],
        ];

        // Find some service ids to attach (prefer 1 if exists)
        $availableServiceIds = Service::pluck('id')->toArray();
        $prefer = in_array(1, $availableServiceIds) ? 1 : ($availableServiceIds[0] ?? null);

        foreach ($drivers as $d) {
            $password = bcrypt('password');
            $driver = Driver::updateOrCreate([
                'email' => $d['email'],
            ], [
                'name' => $d['name'],
                'password' => $password,
            ]);

            // attach a service if available
            if ($prefer) {
                try {
                    $driver->services()->syncWithoutDetaching([$prefer]);
                } catch (\Throwable $e) {
                    // ignore if pivot table missing
                }
            }
        }
    }
}
