<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // create pivot table if it doesn't already exist (make migration idempotent)
        if (!Schema::hasTable('car_wash_driver_driver_vehicle')) {
            Schema::create('car_wash_driver_driver_vehicle', function (Blueprint $table) {
                $table->id();
                $table->foreignId('car_wash_driver_id')->constrained('car_wash_drivers')->cascadeOnDelete();
                $table->foreignId('driver_vehicle_id')->constrained('driver_vehicles')->cascadeOnDelete();
                $table->timestamps();
                // use a short index name to avoid MySQL identifier length limits
                $table->unique(['car_wash_driver_id', 'driver_vehicle_id'], 'cwdrv_driverveh_unique');
            });
        }

        // migrate existing assignments from driver_vehicles.car_wash_driver_id into pivot (if column exists)
        if (Schema::hasColumn('driver_vehicles', 'car_wash_driver_id') && Schema::hasTable('car_wash_driver_driver_vehicle')) {
            $rows = DB::table('driver_vehicles')->whereNotNull('car_wash_driver_id')->get(['id','car_wash_driver_id']);
            foreach ($rows as $r) {
                DB::table('car_wash_driver_driver_vehicle')->updateOrInsert([
                    'car_wash_driver_id' => $r->car_wash_driver_id,
                    'driver_vehicle_id' => $r->id,
                ], ['created_at' => now(), 'updated_at' => now()]);
            }

            Schema::table('driver_vehicles', function (Blueprint $table) {
                if (Schema::hasColumn('driver_vehicles', 'car_wash_driver_id')) {
                    // drop foreign if exists, wrapped in try/catch since some platforms throw if not found
                    try { $table->dropForeign(['car_wash_driver_id']); } catch (\Throwable $e) {}
                    $table->dropColumn('car_wash_driver_id');
                }
            });
        }
    }

    public function down(): void
    {
        // add back car_wash_driver_id column (best-effort)
        if (!Schema::hasColumn('driver_vehicles', 'car_wash_driver_id')) {
            Schema::table('driver_vehicles', function (Blueprint $table) {
                $table->foreignId('car_wash_driver_id')->nullable()->after('id');
            });

            // attempt to repopulate from pivot (first matching)
            $pivot = DB::table('car_wash_driver_driver_vehicle')->get();
            foreach ($pivot as $p) {
                DB::table('driver_vehicles')->where('id', $p->driver_vehicle_id)->update(['car_wash_driver_id' => $p->car_wash_driver_id]);
            }
        }

        Schema::dropIfExists('car_wash_driver_driver_vehicle');
    }
};
