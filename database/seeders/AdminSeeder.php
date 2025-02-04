<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Check if admin exists
        if (!User::where('email', 'admin@kanma.in')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@kanma.in',
                'password' => Hash::make('admin123'), // Change this in production
                'phone_number' => '1234567890',
                'role' => 'admin',
            ]);
            
            $this->command->info('Admin user created successfully!');
        } else {
            $this->command->info('Admin user already exists.');
        }
    }
} 