<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('branch_products', function (Blueprint $table) {
            // Add stock column if it doesn't exist
            if (!Schema::hasColumn('branch_products', 'stock')) {
                $table->integer('stock')->default(0)->after('price');
            }
            
            // Add is_active column if it doesn't exist
            if (!Schema::hasColumn('branch_products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('stock');
            }
        });
    }

    public function down()
    {
        Schema::table('branch_products', function (Blueprint $table) {
            $table->dropColumn(['stock', 'is_active']);
        });
    }
}; 