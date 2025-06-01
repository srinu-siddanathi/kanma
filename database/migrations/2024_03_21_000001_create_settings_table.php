<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('string');
            $table->string('group')->default('general');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'delivery_charge_per_km',
                'value' => '10',
                'type' => 'float',
                'group' => 'delivery',
                'description' => 'Delivery charge per kilometer',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'service_radius_km',
                'value' => '10',
                'type' => 'float',
                'group' => 'delivery',
                'description' => 'Maximum service radius from branch in kilometers',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}; 