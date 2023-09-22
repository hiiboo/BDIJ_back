<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\User;
use \App\Models\Booking;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // relate reviewer_id from users table use foreinIdFor
            $table->foreignIdFor(User::class, 'reviewer_id')->constrained('users');
            // relate reviewee_id from users table
            $table->foreignIdFor(User::class, 'reviewee_id')->constrained('users');
            // relate booking_id from bookings table
            $table->foreignIdFor(Booking::class, 'booking_id')->constrained('bookings');
            // rating 1-5
            $table->integer('rating')->nullable();
            // review content
            $table->text('content');
            $table->unique(['reviewer_id', 'reviewee_id', 'booking_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
