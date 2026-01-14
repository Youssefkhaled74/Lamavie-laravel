<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'pickup_driver_id')) {
                $table->unsignedBigInteger('pickup_driver_id')->nullable()->after('driver_id');
                $table->foreign('pickup_driver_id')->references('id')->on('drivers')->nullOnDelete();
            }
            if (!Schema::hasColumn('bookings', 'delivery_driver_id')) {
                $table->unsignedBigInteger('delivery_driver_id')->nullable()->after('pickup_driver_id');
                $table->foreign('delivery_driver_id')->references('id')->on('drivers')->nullOnDelete();
            }
        });

        // Migrate existing driver_id values into pickup_driver_id for backward compatibility
        if (Schema::hasColumn('bookings', 'driver_id') && Schema::hasColumn('bookings', 'pickup_driver_id')) {
            DB::statement('UPDATE bookings SET pickup_driver_id = driver_id WHERE pickup_driver_id IS NULL AND driver_id IS NOT NULL');
        }
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'delivery_driver_id')) {
                try { $table->dropForeign(['delivery_driver_id']); } catch (\Throwable $e) {}
                $table->dropColumn('delivery_driver_id');
            }
            if (Schema::hasColumn('bookings', 'pickup_driver_id')) {
                try { $table->dropForeign(['pickup_driver_id']); } catch (\Throwable $e) {}
                $table->dropColumn('pickup_driver_id');
            }
        });
    }
};
