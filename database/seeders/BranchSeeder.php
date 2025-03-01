<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BranchSeeder extends Seeder
{
    public function run()
    {
        // Create Madhapur Branch and Manager
        $madhapurManager = User::create([
            'name' => 'Madhapur Manager',
            'email' => 'madhapur.manager@kanma.in',
            'phone' => '9876543210',
            'password' => Hash::make('password'),
            'role' => 'branch_manager',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'Kanma Madhapur',
            'address' => '2nd Floor, Building 123, Madhapur Main Road, Hyderabad',
            'phone' => '9876543210',
            'email' => 'madhapur@kanma.in',
            'latitude' => 17.4486,
            'longitude' => 78.3908,
            'is_active' => true,
            'user_id' => $madhapurManager->id,
        ]);

        // Create Gachibowli Branch and Manager
        $gachibowliManager = User::create([
            'name' => 'Gachibowli Manager',
            'email' => 'gachibowli.manager@kanma.in',
            'phone' => '9876543211',
            'password' => Hash::make('password'),
            'role' => 'branch_manager',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'Kanma Gachibowli',
            'address' => 'Shop No 5, Ground Floor, Tech Park, Gachibowli, Hyderabad',
            'phone' => '9876543211',
            'email' => 'gachibowli@kanma.in',
            'latitude' => 17.4400,
            'longitude' => 78.3489,
            'is_active' => true,
            'user_id' => $gachibowliManager->id,
        ]);

        // Create Kukatpally Branch and Manager
        $kukatpallyManager = User::create([
            'name' => 'Kukatpally Manager',
            'email' => 'kukatpally.manager@kanma.in',
            'phone' => '9876543212',
            'password' => Hash::make('password'),
            'role' => 'branch_manager',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'Kanma Kukatpally',
            'address' => '1st Floor, Metro Station Complex, Kukatpally, Hyderabad',
            'phone' => '9876543212',
            'email' => 'kukatpally@kanma.in',
            'latitude' => 17.4849,
            'longitude' => 78.4138,
            'is_active' => true,
            'user_id' => $kukatpallyManager->id,
        ]);
    }
} 