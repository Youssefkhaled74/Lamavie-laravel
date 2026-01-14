<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('carpet_material', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->nullable()->after('service_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carpet_material', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
