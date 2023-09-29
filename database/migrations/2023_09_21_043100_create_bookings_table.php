<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\User;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            // relate guide_id from users table use foreinIdFor
            $table->foreignIdFor(User::class, 'guide_id')->constrained('users');
            // relate guest_id from users table
            $table->foreignIdFor(User::class, 'guest_id')->constrained('users');
            // start_time
            $table->timestamp('start_time')->nullable();
            // end_time
            $table->timestamp('end_time')->nullable();
            // actual_start_time
            $table->timestamp('actual_start_time')->nullable();
            // comment
            $table->text('comment')->nullable();
            // total_guests
            $table->integer('total_guests')->nullable();
            // total_amount
            $table->integer('total_amount');
            $table->enum('status', ['offer-pending', 'accepted','started', 'finished', 'reviewed', 'cancelled'])->nullable();
            $table->boolean('guest_booking_confirmation')->default(false);
            // guide_booking_confirmation
            $table->boolean('guide_booking_confirmation')->default(false);
            // guest_start_confirmation
            $table->boolean('start_confirmation')->default(false);
            // guest_reviewed
            $table->boolean('guest_reviewed')->default(false);
            // guide_reviewed
            $table->boolean('guide_reviewed')->default(false);
            $table->softDeletes(); // add this line
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
