<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            AreaSeeder::class,
            PermissionSeeder::class,
            CarWashDriverSeeder::class,
            DriversSeeder::class,
            LabsSeeder::class,
            BookingsSeeder::class,
            \Database\Seeders\UserUniqueCodeSeeder::class,
        ]);
    }
}
