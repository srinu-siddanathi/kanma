<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class HomePageSeeder extends Seeder
{
    public function run()
    {
        // Create Categories
        $categories = [
            [
                'name' => 'Fruits & Vegetables',
                'slug' => 'fruits-and-vegetables',
                'icon_url' => 'icons/fruits.png',
                'image_url' => 'categories/fruits.jpg',
                'display_order' => 1,
                'is_active' => true
            ],
            [
                'name' => 'Dairy, Bread & Eggs',
                'slug' => 'dairy-bread-and-eggs',
                'icon_url' => 'icons/dairy.png',
                'image_url' => 'categories/dairy.jpg',
                'display_order' => 2,
                'is_active' => true
            ],
            [
                'name' => 'Cold Drinks & Juices',
                'slug' => 'cold-drinks-and-juices',
                'icon_url' => 'icons/drinks.png',
                'image_url' => 'categories/drinks.jpg',
                'display_order' => 3,
                'is_active' => true
            ],
            [
                'name' => 'Snacks & Munchies',
                'slug' => 'snacks-and-munchies',
                'icon_url' => 'icons/snacks.png',
                'image_url' => 'categories/snacks.jpg',
                'display_order' => 4,
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']], // Find by slug
                $category // Update or create with all data
            );
        }

        // Create Featured Products with Variants
        $products = [
            [
                'name' => 'Tata Salt',
                'slug' => 'tata-salt',
                'description' => 'Iodized Salt for Cooking',
                'image_path' => 'products/tata-salt.jpg',
                'category_id' => 1,
                'is_active' => true,
                'is_featured' => true,
                'variants' => [
                    [
                        'unit' => 'g',
                        'quantity' => 500,
                        'price' => 25.00,
                        'stock' => 100,
                        'is_active' => true,
                        'discount_percentage' => 10
                    ],
                    [
                        'unit' => 'kg',
                        'quantity' => 1,
                        'price' => 45.00,
                        'stock' => 50,
                        'is_active' => true,
                        'discount_percentage' => 5
                    ]
                ]
            ],
            [
                'name' => 'Kurkure Puffcorn',
                'slug' => 'kurkure-puffcorn',
                'description' => 'Yummy Corn Puffs',
                'image_path' => 'products/kurkure.jpg',
                'category_id' => 4,
                'is_active' => true,
                'is_featured' => true,
                'variants' => [
                    [
                        'unit' => 'g',
                        'quantity' => 50,
                        'price' => 22.00,
                        'stock' => 200,
                        'is_active' => true,
                        'discount_percentage' => 0
                    ],
                    [
                        'unit' => 'g',
                        'quantity' => 100,
                        'price' => 40.00,
                        'stock' => 150,
                        'is_active' => true,
                        'discount_percentage' => 15
                    ]
                ]
            ],
            [
                'name' => 'Fresh Tomatoes',
                'slug' => 'fresh-tomatoes',
                'description' => 'Farm Fresh Red Tomatoes',
                'image_path' => 'products/tomatoes.jpg',
                'category_id' => 1,
                'is_active' => true,
                'is_featured' => true,
                'variants' => [
                    [
                        'unit' => 'kg',
                        'quantity' => 1,
                        'price' => 40.00,
                        'stock' => 100,
                        'is_active' => true,
                        'discount_percentage' => 20
                    ]
                ]
            ]
        ];

        foreach ($products as $productData) {
            $variants = $productData['variants'];
            unset($productData['variants']);
            
            $product = Product::updateOrCreate(
                ['slug' => $productData['slug']], // Find by slug
                $productData
            );

            foreach ($variants as $variantData) {
                // Calculate discounted price
                if ($variantData['discount_percentage'] > 0) {
                    $variantData['discounted_price'] = $variantData['price'] * 
                        (1 - $variantData['discount_percentage'] / 100);
                }
                
                // Update or create variant based on unit and quantity
                $product->variants()->updateOrCreate(
                    [
                        'unit' => $variantData['unit'],
                        'quantity' => $variantData['quantity']
                    ],
                    $variantData
                );
            }
        }
    }
} 