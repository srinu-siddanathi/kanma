<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // First, modify the column to allow NULL temporarily
        DB::statement("ALTER TABLE chat_messages MODIFY COLUMN type ENUM('text', 'voice', 'image', 'schedule') NULL");
        
        // Then, set it back to NOT NULL
        DB::statement("ALTER TABLE chat_messages MODIFY COLUMN type ENUM('text', 'voice', 'image', 'schedule') NOT NULL");
    }

    public function down()
    {
        // Remove 'schedule' from the ENUM
        DB::statement("ALTER TABLE chat_messages MODIFY COLUMN type ENUM('text', 'voice', 'image') NOT NULL");
    }
}; 