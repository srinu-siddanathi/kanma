<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('store_latitude', 10, 8)->nullable();
            $table->decimal('store_longitude', 11, 8)->nullable();
            $table->decimal('service_radius', 8, 2)->default(5.00); // in kilometers
            $table->decimal('delivery_charge', 8, 2)->default(0.00);
            $table->decimal('min_order_amount', 8, 2)->default(0.00);
            $table->string('currency', 3)->default('INR');
            $table->string('currency_symbol', 5)->default('₹');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}; 