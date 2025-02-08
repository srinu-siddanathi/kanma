<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class BranchProductController extends Controller
{
    public function index()
    {
        $branch = auth()->user()->branch;
        return response()->json([
            'data' => $branch->products()->with(['category', 'subcategory'])->paginate(20),
            'message' => 'Products retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'description' => 'nullable|string'
        ]);

        $product = auth()->user()->branch->products()->create($validated);

        return response()->json([
            'data' => $product,
            'message' => 'Product created successfully'
        ], 201);
    }

    public function addProduct(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'nullable|numeric', // Optional, will use product's default price if not provided
            'is_active' => 'boolean'
        ]);

        $branch = $request->user()->branch;
        $product = Product::findOrFail($validated['product_id']);

        // Add product to branch with custom or default price
        $branch->products()->attach($product->id, [
            'price' => $validated['price'] ?? $product->price,
            'is_active' => $validated['is_active'] ?? true
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to branch successfully'
        ]);
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'price' => 'required|numeric',
            'is_active' => 'boolean'
        ]);

        $branch = $request->user()->branch;

        // Update branch-specific price and status
        $branch->products()->updateExistingPivot($product->id, [
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? true
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully'
        ]);
    }
} 