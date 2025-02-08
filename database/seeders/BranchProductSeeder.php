<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Product;

class BranchProductSeeder extends Seeder
{
    public function run()
    {
        $branches = Branch::all();
        $products = Product::all();

        foreach ($branches as $branch) {
            foreach ($products as $product) {
                $branch->products()->attach($product->id, [
                    'price' => $product->price,
                    'is_active' => true
                ]);
            }
        }
    }
} 