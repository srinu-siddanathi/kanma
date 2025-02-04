<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run()
    {
        // Katha Plans
        SubscriptionPlan::create([
            'name' => 'Weekly Katha',
            'type' => 'katha',
            'description' => 'Basic weekly subscription with free delivery within 3KM',
            'price' => 99,
            'validity_days' => 7,
            'wallet_addon' => 0,
            'free_orders' => 0,
            'free_delivery_radius' => 3,
            'is_active' => true
        ]);

        SubscriptionPlan::create([
            'name' => 'Monthly Katha Basic',
            'type' => 'katha',
            'description' => 'Monthly subscription with 4 free orders and delivery within 15KM',
            'price' => 299,
            'validity_days' => 30,
            'wallet_addon' => 0,
            'free_orders' => 4,
            'free_delivery_radius' => 15,
            'is_active' => true
        ]);

        SubscriptionPlan::create([
            'name' => 'Monthly Katha Premium',
            'type' => 'katha',
            'description' => 'Premium monthly subscription with wallet addon and 8 free orders',
            'price' => 499,
            'validity_days' => 30,
            'wallet_addon' => 100,
            'free_orders' => 8,
            'free_delivery_radius' => 15,
            'is_active' => true
        ]);

        // O2 Plan
        SubscriptionPlan::create([
            'name' => 'O2 Service',
            'type' => 'o2',
            'description' => 'Special service for elderly and disabled users with chat-based ordering',
            'price' => 0,
            'validity_days' => 30,
            'wallet_addon' => 0,
            'free_orders' => 0,
            'free_delivery_radius' => 0,
            'is_active' => true
        ]);
    }
} 