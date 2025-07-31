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
        Schema::create('user_device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_token', 255);
            $table->enum('device_type', ['android', 'ios', 'web'])->default('android');
            $table->string('app_version', 20)->nullable();
            $table->string('device_model', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate tokens for same user
            $table->unique(['user_id', 'device_token']);
            
            // Indexes for better performance
            $table->index('device_token');
            $table->index('user_id');
            $table->index('is_active');
            $table->index('device_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_device_tokens');
    }
}; 