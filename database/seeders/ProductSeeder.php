<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $branches = Branch::all();
        $categories = Category::with('subcategories')->get();

        foreach ($branches as $branch) {
            foreach ($categories as $category) {
                foreach (range(1, 3) as $i) {
                    Product::create([
                        'branch_id' => $branch->id,
                        'category_id' => $category->id,
                        'subcategory_id' => $category->subcategories->random()->id,
                        'name' => "Product {$i} - {$category->name} ({$branch->name})",
                        'description' => "Description for Product {$i} in {$category->name} category",
                        'price' => rand(100, 1000),
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
} 