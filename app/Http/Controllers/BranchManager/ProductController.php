<?php

namespace App\Http\Controllers\BranchManager;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'subcategory'])
            ->where('branch_id', auth()->user()->branch_id)
            ->latest()
            ->paginate(10);

        return view('branch-manager.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::with('subcategories')
            ->where('is_active', true)
            ->get();
        
        // Debug the data
        \Log::info('Categories with subcategories:', $categories->toArray());
        
        return view('branch-manager.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
        ]);

        $product = auth()->user()->branch->products()->create($validated);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->update(['image_path' => $path]);
        }

        return redirect()
            ->route('branch.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        // Ensure the product belongs to the branch manager's branch
        if ($product->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        return view('branch-manager.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        // Ensure the product belongs to the branch manager's branch
        if ($product->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->update(['image_path' => $path]);
        }

        return redirect()
            ->route('branch.products.index')
            ->with('success', 'Product updated successfully');
    }
} 