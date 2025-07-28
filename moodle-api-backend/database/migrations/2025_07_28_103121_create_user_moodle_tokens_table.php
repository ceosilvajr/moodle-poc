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
        // This table stores the Moodle authentication token linked to your mobile app's user.
        // The 'mobile_user_id' should correspond to the primary key of your own 'users' table.
        Schema::create('user_moodle_tokens', function (Blueprint $table) {
            $table->string('mobile_user_id')->primary(); // Your mobile app's internal user ID
            $table->string('moodle_token'); // The Moodle API token
            $table->integer('moodle_user_id')->nullable(); // Moodle's internal user ID for the linked account
            $table->string('moodle_username')->nullable(); // Moodle username for reference
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_moodle_tokens');
    }
};
