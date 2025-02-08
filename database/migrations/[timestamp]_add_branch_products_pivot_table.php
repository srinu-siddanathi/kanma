<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('branch_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2)->nullable(); // Optional branch-specific price
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add unique constraint to prevent duplicate entries
            $table->unique(['branch_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_products');
    }
}; 