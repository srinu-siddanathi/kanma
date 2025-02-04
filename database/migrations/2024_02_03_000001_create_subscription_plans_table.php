<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'katha' or 'o2'
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('validity_days');
            $table->decimal('wallet_addon', 10, 2)->default(0);
            $table->integer('free_orders')->default(0);
            $table->integer('free_delivery_radius')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
}; 