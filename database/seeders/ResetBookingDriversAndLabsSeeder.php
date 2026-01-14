<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class ResetBookingDriversAndLabsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only update bookings that have any of these fields set to avoid unnecessary writes
        $query = Booking::query()->where(function ($q) {
            $q->whereNotNull('driver_id')
              ->orWhereNotNull('pickup_driver_id')
              ->orWhereNotNull('delivery_driver_id')
              ->orWhereNotNull('lab_assigned_at')
              ->orWhereNotNull('lab_arrived_at')
              ->orWhereNotNull('lab_picked_at')
              ->orWhereNotNull('driver_collected_at');
        });

        $affected = $query->update([
            'driver_id' => null,
            'pickup_driver_id' => null,
            'delivery_driver_id' => null,
            'lab_assigned_at' => null,
            'lab_arrived_at' => null,
            'lab_picked_at' => null,
            'driver_collected_at' => null,
        ]);

        Log::info('ResetBookingDriversAndLabsSeeder completed', ['rows_affected' => $affected]);
    }
}
