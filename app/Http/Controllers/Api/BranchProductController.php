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
} 