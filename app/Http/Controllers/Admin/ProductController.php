<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Traits\HasImageUpload;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use HasImageUpload;

    public function index()
    {
        $products = Product::with(['category', 'subcategory', 'branches'])
            ->whereNull('shop_id')
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::with('subcategories')->get();
        $branches = Branch::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'variants' => 'required|array|min:1',
            'variants.*.quantity' => 'required|numeric|min:0',
            'variants.*.unit' => 'required|in:g,kg,ml,l',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'shop_id' => auth()->user()->shop->id ?? null,
            'is_active' => true,
        ]);

        foreach ($validated['variants'] as $variantData) {
            $product->variants()->create($variantData);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::with('subcategories')->get();
        $branches = Branch::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories', 'branches'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.quantity' => 'required|numeric|min:0',
            'variants.*.unit' => 'required|in:g,kg,ml,l',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
        ]);

        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
        ]);

        // Update existing variants and create new ones
        foreach ($validated['variants'] as $variantData) {
            if (isset($variantData['id'])) {
                $product->variants()->where('id', $variantData['id'])->update([
                    'quantity' => $variantData['quantity'],
                    'unit' => $variantData['unit'],
                    'price' => $variantData['price'],
                    'stock' => $variantData['stock'],
                ]);
            } else {
                $product->variants()->create($variantData);
            }
        }

        // Delete variants that weren't included in the update
        $updatedVariantIds = collect($validated['variants'])->pluck('id')->filter();
        $product->variants()->whereNotIn('id', $updatedVariantIds)->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        // Delete image if exists
        $this->deleteImage($product->image_path);
        
        $product->branches()->detach();
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }

    public function verify(Product $product)
    {
        $product->update([
            'status' => Product::STATUS_VERIFIED,
            'rejection_reason' => null
        ]);

        return back()->with('success', 'Product has been verified successfully');
    }

    public function reject(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $product->update([
            'status' => Product::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason']
        ]);

        return back()->with('success', 'Product has been rejected successfully');
    }

    public function verifyMultiple(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        Product::whereIn('id', $validated['product_ids'])
            ->update([
                'status' => Product::STATUS_VERIFIED,
                'rejection_reason' => null
            ]);

        return back()->with('success', count($validated['product_ids']) . ' products have been verified successfully');
    }

    public function show(Product $product)
    {
        return response()->json([
            'data' => $product->load(['category', 'subcategory'])
        ]);
    }
} 