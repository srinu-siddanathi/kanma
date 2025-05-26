<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otps', function (Blueprint $table) {
            // Add new columns
            $table->text('registration_data')->nullable()->after('otp');
            $table->string('type')->default('login')->after('registration_data');
            
            // Rename verified to is_used if it exists
            if (Schema::hasColumn('otps', 'verified')) {
                $table->renameColumn('verified', 'is_used');
            } else {
                $table->boolean('is_used')->default(false)->after('expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('otps', function (Blueprint $table) {
            $table->dropColumn(['registration_data', 'type']);
            
            if (Schema::hasColumn('otps', 'is_used')) {
                $table->renameColumn('is_used', 'verified');
            }
        });
    }
}; 