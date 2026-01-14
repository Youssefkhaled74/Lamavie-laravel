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
        Schema::create('type_of_packages', function (Blueprint $table) {
            $table->id();
            $table->json('name')->comment('Multilingual name field for English and Arabic');
            $table->foreignId('service_category_id')->constrained()->onDelete('cascade')->comment('Foreign key referencing service_categories table');
            $table->decimal('price', 8, 2)->nullable()->comment('Price of the package type, optional');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_of_packages');
    }
};
