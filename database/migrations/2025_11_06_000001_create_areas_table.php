<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            // Store localized names as JSON: {"en": "Name in English", "ar": "Name in Arabic"}
            $table->json('name');
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('areas');
    }
};
