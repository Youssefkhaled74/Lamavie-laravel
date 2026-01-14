<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class BookingsSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have at least one user to attach bookings to
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test.user@example.com',
                'phone' => '+201099988877',
                'password' => bcrypt('password'),
            ]);
        }

        // Create a booking for service_id = 1 (dry-clean-like)
        $firstDriver = \App\Models\Driver::first();

        // Ensure there is at least one Service, ServiceCategory and ServiceType
        $service = \App\Models\Service::first();
        if (! $service) {
            $service = \App\Models\Service::create([
                'name' => ['en' => 'Default Service'],
            ]);
        }

        $serviceCategory = \App\Models\ServiceCategory::where('service_id', $service->id)->first();
        if (! $serviceCategory) {
            $serviceCategory = \App\Models\ServiceCategory::create([
                'name' => ['en' => 'Default Category'],
                'service_id' => $service->id,
            ]);
        }

        $serviceType = \App\Models\ServiceType::where('service_id', $service->id)->first();
        if (! $serviceType) {
            $serviceType = \App\Models\ServiceType::create([
                'name' => ['en' => 'Default Type'],
                'service_id' => $service->id,
            ]);
        }

        Booking::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'service_type_id' => $serviceType->id,
            'service_category_id' => $serviceCategory->id,
            'status' => 'pending',
            'driver_id' => $firstDriver ? $firstDriver->id : null,
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'total' => 100.00,
            'payload_data' => [
                'item' => ['Pants'],
                'price' => [66.00],
                'quantity' => [1],
                'pickup_location' => 'Maadi, Cairo',
                'delivery_location' => 'Maadi, Cairo',
                'pickup_date' => now()->toDateString(),
                'pickup_time' => '09:00',
                'clothes_returned' => 'folded'
            ],
        ]);

        // Create a booking for a second service (if exists) or reuse the first
        $serviceForCar = \App\Models\Service::find(3) ?: $service;
        $serviceCategoryForCar = \App\Models\ServiceCategory::where('service_id', $serviceForCar->id)->first() ?: $serviceCategory;
        $serviceTypeForCar = \App\Models\ServiceType::where('service_id', $serviceForCar->id)->first() ?: $serviceType;

        Booking::create([
            'user_id' => $user->id,
            'service_id' => $serviceForCar->id,
            'service_type_id' => $serviceTypeForCar->id,
            'service_category_id' => $serviceCategoryForCar->id,
            'status' => 'pending',
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'total' => 250.00,
            'payload_data' => [
                'car_wash_type' => 'normalWash',
                'number_of_cars' => 1,
                'place_of_cleaning' => 'Home',
                'pickup_date' => now()->addDays(1)->toDateString(),
                'pickup_time' => '15:00',
            ],
        ]);
    }
}
