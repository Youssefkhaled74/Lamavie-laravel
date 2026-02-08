<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('your_items', function (Blueprint $table) {
            $table->decimal('washing_price', 8, 2)->nullable()->after('price');
            $table->decimal('ironing_price', 8, 2)->nullable()->after('washing_price');
        });
    }

    public function down(): void
    {
        Schema::table('your_items', function (Blueprint $table) {
            $table->dropColumn(['washing_price', 'ironing_price']);
        });
    }
};
