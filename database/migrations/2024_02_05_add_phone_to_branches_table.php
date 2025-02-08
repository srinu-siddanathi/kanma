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
        Schema::table('branches', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('address');
            // Drop the old contact_number column if it exists
            if (Schema::hasColumn('branches', 'contact_number')) {
                $table->dropColumn('contact_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('phone');
            // Recreate the old contact_number column if needed
            $table->string('contact_number')->nullable();
        });
    }
}; 