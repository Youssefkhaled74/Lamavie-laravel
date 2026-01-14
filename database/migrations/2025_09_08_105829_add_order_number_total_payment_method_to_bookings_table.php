<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('order_number')->unique()->after('id');
            $table->decimal('total', 10, 2)->default(0.00)->after('status');
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('total');
            $table->foreign('payment_method_id')->references('id')->on('payments_method')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['order_number', 'total', 'payment_method_id']);
        });
    }
};
