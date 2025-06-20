<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert minimum order amount setting
        DB::table('settings')->insert([
            'key' => 'minimum_order_amount',
            'value' => '100',
            'type' => 'float',
            'group' => 'delivery',
            'description' => 'Minimum order amount required for free delivery',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove minimum order amount setting
        DB::table('settings')->where('key', 'minimum_order_amount')->delete();
    }
};
