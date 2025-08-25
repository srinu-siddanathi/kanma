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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email_type'); // 'order_placed', 'order_confirmed', 'password_reset', etc.
            $table->string('recipient_type'); // 'customer', 'branch_manager', 'admin', etc.
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->string('subject');
            $table->text('content')->nullable(); // Store email content for debugging
            $table->json('metadata')->nullable(); // Store additional data like order_id, user_id, etc.
            $table->enum('status', ['pending', 'sent', 'failed', 'bounced'])->default('pending');
            $table->text('error_message')->nullable(); // Store error details if failed
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->string('message_id')->nullable(); // Email provider's message ID
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['email_type', 'status']);
            $table->index(['recipient_email']);
            $table->index(['sent_at']);
            $table->index(['recipient_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
}; 