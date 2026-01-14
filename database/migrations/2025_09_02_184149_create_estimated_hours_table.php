<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('estimated_hours', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->foreignId('service_category_id')->constrained('service_categories')->onDelete('cascade');
            $table->decimal('price', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('estimated_hours');
    }
};
