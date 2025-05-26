<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $categories = [
            [
                'name' => 'Fresh Fruits',
                'slug' => Str::slug('Fresh Fruits'),
                'description' => 'Fresh and organic fruits',
                'image_url' => 'images/categories/fruits.jpg',
                'is_active' => true,
                'display_order' => 1
            ],
            [
                'name' => 'Fresh Vegetables',
                'slug' => Str::slug('Fresh Vegetables'),
                'description' => 'Fresh and organic vegetables',
                'image_url' => 'images/categories/vegetables.jpg',
                'is_active' => true,
                'display_order' => 2
            ],
            [
                'name' => 'Dairy Products',
                'slug' => Str::slug('Dairy Products'),
                'description' => 'Fresh dairy products',
                'image_url' => 'images/categories/dairy.jpg',
                'is_active' => true,
                'display_order' => 3
            ],
            [
                'name' => 'Bakery Items',
                'slug' => Str::slug('Bakery Items'),
                'description' => 'Fresh baked goods',
                'image_url' => 'images/categories/bakery.jpg',
                'is_active' => true,
                'display_order' => 4
            ],
            [
                'name' => 'Beverages',
                'slug' => Str::slug('Beverages'),
                'description' => 'Refreshing drinks',
                'image_url' => 'images/categories/beverages.jpg',
                'is_active' => true,
                'display_order' => 5
            ],
            [
                'name' => 'Snacks',
                'slug' => Str::slug('Snacks'),
                'description' => 'Delicious snacks',
                'image_url' => 'images/categories/snacks.jpg',
                'is_active' => true,
                'display_order' => 6
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 