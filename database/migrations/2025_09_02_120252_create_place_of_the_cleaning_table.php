<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('place_of_the_cleaning', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->foreignId('service_category_id')->constrained('service_categories')->onDelete('cascade');
            $table->decimal('price', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('place_of_the_cleaning');
    }
};
