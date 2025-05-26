<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;

class HomePageProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Create trending products
        $trendingProducts = [
            [
                'name' => 'Fresh Organic Apples',
                'slug' => Str::slug('Fresh Organic Apples'),
                'description' => 'Sweet and crispy organic apples',
                'image_path' => 'images/products/apples.jpg',
                'is_active' => true,
                'is_trending' => true,
                'sales_count' => 150,
                'variants' => [
                    ['quantity' => 1, 'unit' => 'kg', 'price' => 4.99],
                    ['quantity' => 5, 'unit' => 'kg', 'price' => 22.99]
                ]
            ],
            [
                'name' => 'Organic Bananas',
                'slug' => Str::slug('Organic Bananas'),
                'description' => 'Fresh organic bananas',
                'image_path' => 'images/products/bananas.jpg',
                'is_active' => true,
                'is_trending' => true,
                'sales_count' => 200,
                'variants' => [
                    ['quantity' => 1, 'unit' => 'dozen', 'price' => 3.99],
                    ['quantity' => 2, 'unit' => 'dozen', 'price' => 7.50]
                ]
            ],
            // Add more trending products...
        ];

        // Create best selling products
        $bestSellingProducts = [
            [
                'name' => 'Fresh Milk',
                'slug' => Str::slug('Fresh Milk'),
                'description' => 'Farm fresh milk',
                'image_path' => 'images/products/milk.jpg',
                'is_active' => true,
                'sales_count' => 500,
                'variants' => [
                    ['quantity' => 1, 'unit' => 'liter', 'price' => 2.99],
                    ['quantity' => 2, 'unit' => 'liter', 'price' => 5.50]
                ]
            ],
            [
                'name' => 'Whole Wheat Bread',
                'slug' => Str::slug('Whole Wheat Bread'),
                'description' => 'Freshly baked whole wheat bread',
                'image_path' => 'images/products/bread.jpg',
                'is_active' => true,
                'sales_count' => 450,
                'variants' => [
                    ['quantity' => 1, 'unit' => 'loaf', 'price' => 3.49]
                ]
            ],
            // Add more best selling products...
        ];

        // Create new arrival products
        $newProducts = [
            [
                'name' => 'Organic Honey',
                'slug' => Str::slug('Organic Honey'),
                'description' => 'Pure organic honey',
                'image_path' => 'images/products/honey.jpg',
                'is_active' => true,
                'sales_count' => 50,
                'variants' => [
                    ['quantity' => 500, 'unit' => 'ml', 'price' => 8.99],
                    ['quantity' => 1, 'unit' => 'liter', 'price' => 15.99]
                ]
            ],
            [
                'name' => 'Fresh Eggs',
                'slug' => Str::slug('Fresh Eggs'),
                'description' => 'Farm fresh eggs',
                'image_path' => 'images/products/eggs.jpg',
                'is_active' => true,
                'sales_count' => 75,
                'variants' => [
                    ['quantity' => 6, 'unit' => 'pcs', 'price' => 2.99],
                    ['quantity' => 12, 'unit' => 'pcs', 'price' => 5.50]
                ]
            ],
            // Add more new products...
        ];

        // Insert all products
        foreach (array_merge($trendingProducts, $bestSellingProducts, $newProducts) as $productData) {
            $variants = $productData['variants'];
            unset($productData['variants']);
            
            $product = Product::create($productData);

            foreach ($variants as $variantData) {
                $variantData['product_id'] = $product->id;
                $variantData['is_active'] = true;
                $variantData['stock'] = 100;
                ProductVariant::create($variantData);
            }
        }
    }
} 