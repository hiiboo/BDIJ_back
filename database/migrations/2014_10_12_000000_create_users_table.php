<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // create column called login_type: 1 = email, 2 = facebook, 3 = google 4 = twitter 5 = instagram
            $table-> enum('login_type', [1,2,3,4,5])->default(1);
            $table->string('social_id')->nullable();
            // first name
            $table->string('first_name')->nullable();
            // last name
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('profile_image')->nullable();
            // language proficiency level
            $table->enum(
                'level',
                [
                    'beginner',
                    'elementary',
                    'intermediate',
                    'upper_intermediate',
                    'advanced',
                    'proficiency',
                ]
            )->default('beginner');
            $table->text('introduction')->nullable();
            $table->integer('hourly_rate')->nullable();
            // bitthday
            $table->date('birthday')->nullable();
            // occupation
            $table->string('occupation')->nullable();
            // user type guide and guest
            $table->enum('user_type', ['guide', 'guest'])->default('guest');
            // user status
            $table->enum('status', ['active', 'inactive'])->default('active');
            // user role
            $table->enum('role', ['admin', 'user'])->default('user');
            // latitude and longitude
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // add this line
                });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
