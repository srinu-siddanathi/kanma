<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // For SQLite compatibility, we need to recreate the table
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Create a new table with the updated schema
            Schema::create('chat_messages_new', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('chat_order_id');
                $table->unsignedBigInteger('sender_id');
                $table->enum('type', ['text', 'voice', 'image', 'schedule']);
                $table->text('content');
                $table->string('file_path')->nullable();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamps();
                
                $table->foreign('chat_order_id')->references('id')->on('chat_orders')->onDelete('cascade');
                $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            });
            
            // Copy data from old table to new table
            DB::statement('INSERT INTO chat_messages_new SELECT * FROM chat_messages');
            
            // Drop old table and rename new table
            Schema::drop('chat_messages');
            Schema::rename('chat_messages_new', 'chat_messages');
        } else {
            // For MySQL, use the original approach
            DB::statement("ALTER TABLE chat_messages MODIFY COLUMN type ENUM('text', 'voice', 'image', 'schedule') NULL");
            DB::statement("ALTER TABLE chat_messages MODIFY COLUMN type ENUM('text', 'voice', 'image', 'schedule') NOT NULL");
        }
    }

    public function down()
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Create a new table with the original schema
            Schema::create('chat_messages_old', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('chat_order_id');
                $table->unsignedBigInteger('sender_id');
                $table->enum('type', ['text', 'voice', 'image']);
                $table->text('content');
                $table->string('file_path')->nullable();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamps();
                
                $table->foreign('chat_order_id')->references('id')->on('chat_orders')->onDelete('cascade');
                $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            });
            
            // Copy data back (excluding 'schedule' type messages)
            DB::statement("INSERT INTO chat_messages_old SELECT * FROM chat_messages WHERE type != 'schedule'");
            
            // Drop current table and rename old table
            Schema::drop('chat_messages');
            Schema::rename('chat_messages_old', 'chat_messages');
        } else {
            // For MySQL, use the original approach
            DB::statement("ALTER TABLE chat_messages MODIFY COLUMN type ENUM('text', 'voice', 'image') NOT NULL");
        }
    }
}; 