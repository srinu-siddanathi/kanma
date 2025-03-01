<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing shop-related data
        Product::where('shop_id', '!=', null)->delete();
        Shop::truncate();
        User::where('role', 'shop_owner')->delete();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $shopData = [
            [
                'user' => [
                    'name' => 'Active Shop Owner',
                    'email' => 'active.shop@example.com',
                    'role' => 'shop_owner',
                    'is_active' => true,
                ],
                'shop' => [
                    'name' => 'Active Verified Shop',
                    'description' => 'This is an active and verified shop',
                    'address' => '123 Main Street',
                    'phone' => '1234567890',
                    'email' => 'active.shop@example.com',
                    'is_active' => true,
                    'is_verified' => true,
                    'approval_status' => 'approved',
                ],
                'products_count' => 5,
            ],
            [
                'user' => [
                    'name' => 'Pending Shop Owner',
                    'email' => 'pending.shop@example.com',
                    'role' => 'shop_owner',
                    'is_active' => false,
                ],
                'shop' => [
                    'name' => 'Pending Shop',
                    'description' => 'This shop is pending approval',
                    'address' => '456 Oak Avenue',
                    'phone' => '2345678901',
                    'email' => 'pending.shop@example.com',
                    'is_active' => false,
                    'is_verified' => false,
                    'approval_status' => 'pending',
                ],
                'products_count' => 3,
            ],
            [
                'user' => [
                    'name' => 'Rejected Shop Owner',
                    'email' => 'rejected.shop@example.com',
                    'role' => 'shop_owner',
                    'is_active' => false,
                ],
                'shop' => [
                    'name' => 'Rejected Shop',
                    'description' => 'This shop was rejected',
                    'address' => '789 Pine Road',
                    'phone' => '3456789012',
                    'email' => 'rejected.shop@example.com',
                    'is_active' => false,
                    'is_verified' => false,
                    'approval_status' => 'rejected',
                    'rejection_reason' => 'Incomplete documentation provided',
                ],
                'products_count' => 15,
                'products' => [
                    ['name' => 'Premium Product', 'price' => 299.99],
                    ['name' => 'Budget Product', 'price' => 49.99],
                    ['name' => 'Mid-Range Product', 'price' => 149.99],
                    ['name' => 'Luxury Item', 'price' => 499.99],
                    ['name' => 'Basic Item', 'price' => 29.99],
                ],
            ],
            [
                'user' => [
                    'name' => 'Unverified Shop Owner',
                    'email' => 'unverified.shop@example.com',
                    'role' => 'shop_owner',
                    'is_active' => true,
                ],
                'shop' => [
                    'name' => 'Unverified Shop',
                    'description' => 'This shop is active but not verified',
                    'address' => '321 Elm Street',
                    'phone' => '4567890123',
                    'email' => 'unverified.shop@example.com',
                    'is_active' => true,
                    'is_verified' => false,
                    'approval_status' => 'approved',
                ],
                'products_count' => 4,
            ],
        ];

        $categories = Category::with('subcategories')->get();

        foreach ($shopData as $data) {
            // Create shop owner
            $shopOwner = User::create(array_merge($data['user'], [
                'password' => bcrypt('password'),
            ]));

            // Create shop
            $shop = Shop::create(array_merge($data['shop'], [
                'user_id' => $shopOwner->id,
            ]));

            // Create products
            $productsToCreate = $data['products_count'];
            
            // First create any specific products defined
            if (isset($data['products'])) {
                foreach ($data['products'] as $specificProduct) {
                    $category = $categories->random();
                    $subcategory = $category->subcategories->first();

                    Product::create([
                        'name' => $specificProduct['name'],
                        'slug' => Str::slug($specificProduct['name']),
                        'description' => "A {$specificProduct['name']} from {$shop->name}",
                        'price' => $specificProduct['price'],
                        'category_id' => $category->id,
                        'subcategory_id' => $subcategory->id,
                        'shop_id' => $shop->id,
                        'is_active' => true,
                        'status' => $shop->is_verified ? 'verified' : 'pending',
                    ]);
                    
                    $productsToCreate--;
                }
            }

            // Create remaining generic products
            for ($i = 1; $i <= $productsToCreate; $i++) {
                $category = $categories->random();
                $subcategory = $category->subcategories->first();

                Product::create([
                    'name' => "{$shop->name} Product {$i}",
                    'slug' => Str::slug("{$shop->name} Product {$i}"),
                    'description' => "Product {$i} from {$shop->name}",
                    'price' => rand(50, 500) + 0.99,
                    'category_id' => $category->id,
                    'subcategory_id' => $subcategory->id,
                    'shop_id' => $shop->id,
                    'is_active' => true,
                    'status' => $shop->is_verified ? 'verified' : 'pending',
                ]);
            }
        }
    }
} 