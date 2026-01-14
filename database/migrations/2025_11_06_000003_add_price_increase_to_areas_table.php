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
        if (! Schema::hasTable('areas')) {
            return;
        }

        Schema::table('areas', function (Blueprint $table) {
            if (! Schema::hasColumn('areas', 'price_increase_percentage')) {
                $table->decimal('price_increase_percentage', 5, 2)->default(0)->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('areas')) {
            return;
        }

        Schema::table('areas', function (Blueprint $table) {
            if (Schema::hasColumn('areas', 'price_increase_percentage')) {
                $table->dropColumn('price_increase_percentage');
            }
        });
    }
};
