<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Make price nullable since we're using variant prices now
            $table->decimal('price', 10, 2)->nullable()->change();
            
            // Add a base_unit field to store the default unit for the product
            $table->string('base_unit')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable(false)->change();
            $table->dropColumn('base_unit');
        });
    }
}; 