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

        // Seed 3 featured products with images and updated prices
        $featuredProducts = [
            [
                'name' => 'Coriander leaves with roots',
                'slug' => Str::slug('Coriander leaves with roots'),
                'description' => 'Fresh coriander leaves with roots, 100g',
                'category_id' => $categories->first()?->id,
                'subcategory_id' => $categories->first()?->subcategories->first()?->id,
                'price' => 13,
                'is_active' => true,
                'is_featured' => true,
                'image_path' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=cover&w=400&q=80',
            ],
            [
                'name' => 'Tomato Local',
                'slug' => Str::slug('Tomato Local'),
                'description' => 'Fresh local tomatoes, 500g',
                'category_id' => $categories->first()?->id,
                'subcategory_id' => $categories->first()?->subcategories->first()?->id,
                'price' => 25,
                'is_active' => true,
                'is_featured' => true,
                'image_path' => 'https://images.unsplash.com/photo-1464306076886-debca5e8a6b0?auto=format&fit=cover&w=400&q=80',
            ],
            [
                'name' => 'Fresh Cucumber',
                'slug' => Str::slug('Fresh Cucumber'),
                'description' => 'Crisp and fresh cucumbers, 1kg',
                'category_id' => $categories->first()?->id,
                'subcategory_id' => $categories->first()?->subcategories->first()?->id,
                'price' => 40,
                'is_active' => true,
                'is_featured' => true,
                'image_path' => 'https://images.unsplash.com/photo-1502741338009-cac2772e18bc?auto=format&fit=cover&w=400&q=80',
            ],
        ];
        foreach ($featuredProducts as $data) {
            Product::create($data);
        }

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
                        'price' => rand(10, 200),
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