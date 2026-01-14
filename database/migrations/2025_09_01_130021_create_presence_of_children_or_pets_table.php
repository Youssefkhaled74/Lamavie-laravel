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
        Schema::create('presence_of_children_or_pets', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // JSON field for multilingual names
            $table->unsignedBigInteger('service_category_id');
            $table->decimal('price', 8, 2)->nullable(); // Price with 2 decimal places
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('service_category_id')->references('id')->on('service_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presence_of_children_or_pets');
    }
};
