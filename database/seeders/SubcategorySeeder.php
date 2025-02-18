<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SubcategorySeeder extends Seeder
{
    public function run()
    {
        // Clear existing subcategories
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Subcategory::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $subcategories = [
            // Food subcategories
            [
                'name' => 'Starters',
                'category_name' => 'Food',
            ],
            [
                'name' => 'Main Course',
                'category_name' => 'Food',
            ],
            [
                'name' => 'Rice Items',
                'category_name' => 'Food',
            ],
            // Beverages subcategories
            [
                'name' => 'Hot Drinks',
                'category_name' => 'Beverages',
            ],
            [
                'name' => 'Cold Drinks',
                'category_name' => 'Beverages',
            ],
            // Desserts subcategories
            [
                'name' => 'Ice Creams',
                'category_name' => 'Desserts',
            ],
            [
                'name' => 'Cakes',
                'category_name' => 'Desserts',
            ],
        ];

        foreach ($subcategories as $subcategory) {
            $category = Category::where('name', $subcategory['category_name'])->first();
            
            if ($category) {
                Subcategory::create([
                    'name' => $subcategory['name'],
                    'slug' => Str::slug($subcategory['name']),
                    'description' => 'Description for ' . $subcategory['name'],
                    'category_id' => $category->id,
                    'is_active' => true,
                ]);
            }
        }
    }
} 