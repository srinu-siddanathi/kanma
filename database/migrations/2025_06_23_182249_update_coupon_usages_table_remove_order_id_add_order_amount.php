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
        Schema::table('coupon_usages', function (Blueprint $table) {
            // Remove order_id column
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
            
            // Add order_amount column
            $table->decimal('order_amount', 10, 2)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupon_usages', function (Blueprint $table) {
            // Remove order_amount column
            $table->dropColumn('order_amount');
            
            // Add back order_id column
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
