<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // If phone_number exists and phone doesn't exist
            if (Schema::hasColumn('users', 'phone_number') && !Schema::hasColumn('users', 'phone')) {
                $table->renameColumn('phone_number', 'phone');
            }
            // If both columns exist, merge data and drop phone_number
            else if (Schema::hasColumn('users', 'phone_number') && Schema::hasColumn('users', 'phone')) {
                // Update null phone values with phone_number values if they exist
                DB::statement('UPDATE users SET phone = phone_number WHERE phone IS NULL AND phone_number IS NOT NULL');
                $table->dropColumn('phone_number');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone') && !Schema::hasColumn('users', 'phone_number')) {
                $table->renameColumn('phone', 'phone_number');
            }
        });
    }
}; 