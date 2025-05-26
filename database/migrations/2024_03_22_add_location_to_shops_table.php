<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->json('working_hours')->nullable()->after('longitude');
            $table->decimal('rating', 2, 1)->default(0)->after('working_hours');
            $table->integer('reviews_count')->default(0)->after('rating');
        });
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'working_hours', 'rating', 'reviews_count']);
        });
    }
}; 