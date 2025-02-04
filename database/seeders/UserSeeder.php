<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Branch Managers
        User::create([
            'name' => 'Main Branch Manager',
            'email' => 'manager1@example.com',
            'password' => Hash::make('password'),
            'role' => 'branch_manager',
            'branch_id' => 1,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Downtown Branch Manager',
            'email' => 'manager2@example.com',
            'password' => Hash::make('password'),
            'role' => 'branch_manager',
            'branch_id' => 2,
            'is_active' => true,
        ]);

        // Create some customers
        User::create([
            'name' => 'Customer One',
            'email' => 'customer1@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Customer Two',
            'email' => 'customer2@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_active' => true,
        ]);
    }
} 