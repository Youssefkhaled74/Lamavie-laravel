<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('notifiable'); // Polymorphic relationship for notifiable entities (e.g., User)
            $table->string('type'); // Type of notification (e.g., CanceledBookingNotification)
            $table->string('title'); // Notification title
            $table->text('body'); // Notification body/message
            $table->json('data')->nullable(); // Additional data (e.g., booking_id, order_number)
            $table->string('status')->default('pending'); // Status: pending, sent, failed
            $table->timestamp('sent_at')->nullable(); // When the notification was sent
            $table->text('error_message')->nullable(); // Error message if sending failed
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
        Schema::dropIfExists('notifications');
    }
};
