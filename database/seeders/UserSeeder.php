<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Clear existing users except admin
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        User::where('role', '!=', 'admin')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create customer users
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Customer {$i}",
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password'),
                'role' => 'customer',
                'phone' => '98765' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'is_active' => true,
                'wallet_balance' => 0,
            ]);
        }

        // Create branch managers (if needed)
        $branches = \App\Models\Branch::whereDoesntHave('user')->get();
        foreach ($branches as $index => $branch) {
            $manager = User::create([
                'name' => "Manager " . ($index + 1),
                'email' => "manager" . ($index + 1) . "@kanma.in",
                'password' => Hash::make('password'),
                'role' => 'branch_manager',
                'phone' => '97865' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'is_active' => true,
                'branch_id' => $branch->id,
            ]);

            $branch->update(['user_id' => $manager->id]);
        }
    }
} 