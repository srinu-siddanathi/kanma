<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'subcategory'])
            ->when($request->category_id, function($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->when($request->subcategory_id, function($query, $subcategoryId) {
                return $query->where('subcategory_id', $subcategoryId);
            })
            ->when($request->search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(20);

        return response()->json($products);
    }

    public function show(Product $product)
    {
        return response()->json($product->load(['category', 'subcategory']));
    }
} 