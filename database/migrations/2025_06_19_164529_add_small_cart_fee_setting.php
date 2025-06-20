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
        // Insert small cart fee setting
        DB::table('settings')->insert([
            'key' => 'small_cart_fee',
            'value' => '10',
            'type' => 'float',
            'group' => 'delivery',
            'description' => 'Additional fee for orders below minimum order amount',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove small cart fee setting
        DB::table('settings')->where('key', 'small_cart_fee')->delete();
    }
};
