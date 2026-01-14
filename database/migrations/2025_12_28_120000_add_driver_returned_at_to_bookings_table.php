<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('bookings', 'driver_returned_at')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->timestamp('driver_returned_at')->nullable()->after('driver_collected_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('bookings', 'driver_returned_at')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('driver_returned_at');
            });
        }
    }
};
