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
        // First, clean up any orphaned order items (order items that reference non-existent products)
        DB::statement('DELETE oi FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE p.id IS NULL');
        
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['product_id']);
            
            // Add the foreign key constraint with cascade delete
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Drop the cascade delete foreign key constraint
            $table->dropForeign(['product_id']);
            
            // Add back the original foreign key constraint without cascade delete
            $table->foreign('product_id')->references('id')->on('products');
        });
    }
};
