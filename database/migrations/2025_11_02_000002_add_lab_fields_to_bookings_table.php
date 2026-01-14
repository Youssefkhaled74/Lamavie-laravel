<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('lab_id')->nullable()->after('service_type_id');
            $table->timestamp('lab_assigned_at')->nullable()->after('lab_id');
            $table->timestamp('lab_arrived_at')->nullable()->after('lab_assigned_at');
            $table->timestamp('lab_picked_at')->nullable()->after('lab_arrived_at');

            $table->foreign('lab_id')->references('id')->on('labs')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['lab_id']);
            $table->dropColumn(['lab_id', 'lab_assigned_at', 'lab_arrived_at', 'lab_picked_at']);
        });
    }
};
