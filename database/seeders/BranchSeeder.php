<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        Branch::create([
            'name' => 'Kanma Madhapur',
            'address' => '2nd Floor, Building 123, Madhapur Main Road, Hyderabad',
            'contact_number' => '9876543210',
            'email' => 'madhapur@kanma.in',
            'latitude' => 17.4486,
            'longitude' => 78.3908,
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'Kanma Gachibowli',
            'address' => 'Shop No 5, Ground Floor, Tech Park, Gachibowli, Hyderabad',
            'contact_number' => '9876543211',
            'email' => 'gachibowli@kanma.in',
            'latitude' => 17.4400,
            'longitude' => 78.3489,
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'Kanma Kukatpally',
            'address' => '1st Floor, Metro Station Complex, Kukatpally, Hyderabad',
            'contact_number' => '9876543212',
            'email' => 'kukatpally@kanma.in',
            'latitude' => 17.4849,
            'longitude' => 78.4138,
            'is_active' => true,
        ]);
    }
} 