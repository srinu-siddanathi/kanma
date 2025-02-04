<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    public function up()
    {
        User::where('is_active', null)->update(['is_active' => true]);
    }

    public function down()
    {
        // No need for down method
    }
}; 