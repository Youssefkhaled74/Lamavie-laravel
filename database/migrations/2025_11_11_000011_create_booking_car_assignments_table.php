<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('booking_car_assignments')) {
            Schema::create('booking_car_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
                $table->foreignId('driver_vehicle_id')->constrained('driver_vehicles')->onDelete('cascade');
                $table->foreignId('assigned_by')->nullable()->constrained('admins')->nullOnDelete();
                $table->timestamp('start_at')->nullable();
                $table->timestamp('end_at')->nullable();
                $table->timestamps();

                $table->index(['booking_id', 'driver_vehicle_id']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('booking_car_assignments');
    }
};
