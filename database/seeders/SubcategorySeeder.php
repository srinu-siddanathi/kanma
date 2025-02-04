<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubcategorySeeder extends Seeder
{
    public function run()
    {
        $subcategories = [
            // Food subcategories
            ['name' => 'Main Course', 'category_id' => 1],
            ['name' => 'Appetizers', 'category_id' => 1],
            ['name' => 'Salads', 'category_id' => 1],
            
            // Beverages subcategories
            ['name' => 'Hot Drinks', 'category_id' => 2],
            ['name' => 'Cold Drinks', 'category_id' => 2],
            ['name' => 'Smoothies', 'category_id' => 2],
            
            // Snacks subcategories
            ['name' => 'Chips', 'category_id' => 3],
            ['name' => 'Nuts', 'category_id' => 3],
            
            // Desserts subcategories
            ['name' => 'Cakes', 'category_id' => 4],
            ['name' => 'Ice Cream', 'category_id' => 4],
        ];

        foreach ($subcategories as $subcategory) {
            Subcategory::create([
                'name' => $subcategory['name'],
                'slug' => Str::slug($subcategory['name']),
                'description' => 'Description for ' . $subcategory['name'],
                'category_id' => $subcategory['category_id'],
                'is_active' => true,
            ]);
        }
    }
} 