<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DealProductSeeder extends Seeder
{
    public function run()
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        $dealProducts = [
            [
                'name' => 'Fresh Apples - Special Deal',
                'description' => 'Sweet and juicy apples at a special discounted price. Limited time offer!',
                'price' => 120.00,
                'discount' => 25.00,
                'base_unit' => '1kg',
                'is_deal' => true,
                'deal_end_date' => now()->addDays(7),
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Organic Bananas - Flash Sale',
                'description' => 'Organic bananas from local farms. Get them before they expire!',
                'price' => 80.00,
                'discount' => 30.00,
                'base_unit' => '1kg',
                'is_deal' => true,
                'deal_end_date' => now()->addDays(3),
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Premium Tomatoes - Weekend Special',
                'description' => 'Premium quality tomatoes at unbeatable prices. Weekend special offer!',
                'price' => 60.00,
                'discount' => 20.00,
                'base_unit' => '500g',
                'is_deal' => true,
                'deal_end_date' => now()->addDays(5),
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Fresh Milk - Daily Deal',
                'description' => 'Fresh farm milk delivered daily. Today\'s special price!',
                'price' => 45.00,
                'discount' => 15.00,
                'base_unit' => '1l',
                'is_deal' => true,
                'deal_end_date' => now()->addDays(1),
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Whole Grain Bread - Morning Deal',
                'description' => 'Freshly baked whole grain bread. Morning special!',
                'price' => 35.00,
                'discount' => 40.00,
                'base_unit' => '1pc',
                'is_deal' => true,
                'deal_end_date' => now()->addHours(12),
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($dealProducts as $productData) {
            $category = $categories->random();
            
            Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'category_id' => $category->id,
                'price' => $productData['price'],
                'base_unit' => $productData['base_unit'],
                'is_active' => $productData['is_active'],
                'is_verified' => true,
                'status' => 'verified',
                'is_deal' => $productData['is_deal'],
                'discount' => $productData['discount'],
                'deal_end_date' => $productData['deal_end_date'],
                'is_featured' => $productData['is_featured'],
            ]);
        }

        $this->command->info('Deal products created successfully!');
    }
} 