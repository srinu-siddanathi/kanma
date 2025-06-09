<?php

namespace App\Http\Controllers\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;

class ProductController extends Controller
{
    public function index()
    {
        $products = auth()->user()->shop->products()
            ->with(['category', 'subcategory'])
            ->latest()
            ->paginate(10);

        return view('shop-owner.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::with('subcategories')->get();
        return view('shop-owner.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['shop_id'] = auth()->user()->shop->id;
        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/products'), $filename);
            $validated['image_path'] = 'uploads/products/' . $filename;
        }

        Product::create($validated);

        return redirect()
            ->route('shop-owner.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        if ($product->shop_id !== auth()->user()->shop->id) {
            abort(403, 'You are not authorized to edit this product.');
        }
        
        $categories = Category::with('subcategories')->get();
        return view('shop-owner.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->shop_id !== auth()->user()->shop->id) {
            abort(403, 'You are not authorized to update this product.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                $oldImagePath = public_path($product->image_path);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/products'), $filename);
            $validated['image_path'] = 'uploads/products/' . $filename;
        }

        $product->update($validated);

        return redirect()
            ->route('shop-owner.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        if ($product->shop_id !== auth()->user()->shop->id) {
            abort(403, 'You are not authorized to delete this product.');
        }

        if ($product->image_path) {
            $imagePath = public_path($product->image_path);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $product->delete();

        return redirect()
            ->route('shop-owner.products.index')
            ->with('success', 'Product deleted successfully');
    }
} 