<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Product::truncate();
        DB::table('branch_products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $categories = Category::with('subcategories')->get();
        $branches = Branch::all();

        foreach ($categories as $category) {
            foreach ($category->subcategories as $subcategory) {
                // Create 3 products for each subcategory
                for ($i = 1; $i <= 3; $i++) {
                    $product = Product::create([
                        'name' => "Product {$i} - {$subcategory->name}",
                        'slug' => Str::slug("Product {$i} - {$subcategory->name}"),
                        'description' => "Description for Product {$i} in {$subcategory->name}",
                        'category_id' => $category->id,
                        'subcategory_id' => $subcategory->id,
                        'price' => rand(100, 1000),
                        'is_active' => true,
                    ]);

                    // Attach to all branches with random prices
                    foreach ($branches as $branch) {
                        $branch->products()->attach($product->id, [
                            'price' => rand(100, 1000),
                            'is_active' => true
                        ]);
                    }
                }
            }
        }
    }
} 