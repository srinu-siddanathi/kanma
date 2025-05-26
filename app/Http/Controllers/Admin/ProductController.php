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
        $query = Product::with(['category', 'images']);

        // Apply search filter
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if (request('category')) {
            $query->where('category_id', request('category'));
        }

        // Apply deal filter
        if (request()->has('is_deal')) {
            \Log::info('Deal filter:', [
                'raw_value' => request('is_deal'),
                'query' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
            
            $query->where('is_deal', '=', 1);
        }

        // Apply featured filter
        if (request()->has('is_featured')) {
            \Log::info('Featured filter:', [
                'raw_value' => request('is_featured'),
                'query' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
            
            $query->where('is_featured', '=', 1);
        }

        // Log the query before pagination
        \Log::info('Products query before pagination:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        $products = $query->latest()->paginate(10);
        $categories = Category::orderBy('name')->get();

        // Log the results
        \Log::info('Products results:', [
            'total' => $products->total(),
            'first_page_count' => $products->count(),
            'first_page_items' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'is_deal' => $product->is_deal,
                    'is_featured' => $product->is_featured
                ];
            })->toArray()
        ]);

        return view('admin.products.index', compact('products', 'categories'));
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
            'variants' => 'required|array|min:1',
            'variants.*.quantity' => 'required|numeric|min:0',
            'variants.*.unit' => 'required|in:g,kg,ml,l',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_deal' => 'boolean',
            'is_featured' => 'boolean',
            'discount' => 'nullable|numeric|min:0|max:100|required_if:is_deal,1'
        ]);

        // Get the first variant's price and unit
        $firstVariant = $validated['variants'][0];

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'shop_id' => auth()->user()->shop->id ?? null,
            'is_active' => true,
            'is_deal' => $request->boolean('is_deal'),
            'is_featured' => $request->boolean('is_featured'),
            'discount' => $request->input('discount'),
            'price' => $firstVariant['price'],
            'base_unit' => $firstVariant['quantity'] . $firstVariant['unit']
        ]);

        // Handle multiple image uploads
        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('products', 'public');
            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $index === 0, // First image is primary
                'sort_order' => $index
            ]);
        }

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
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.quantity' => 'required|numeric|min:0',
            'variants.*.unit' => 'required|in:g,kg,ml,l',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_deal' => 'boolean',
            'is_featured' => 'boolean',
            'discount' => 'nullable|numeric|min:0|max:100|required_if:is_deal,1'
        ]);

        // Get the first variant's price and unit
        $firstVariant = $validated['variants'][0];

        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'is_deal' => $request->boolean('is_deal'),
            'is_featured' => $request->boolean('is_featured'),
            'discount' => $request->input('discount'),
            'price' => $firstVariant['price'],
            'base_unit' => $firstVariant['quantity'] . $firstVariant['unit']
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0 && $product->images->isEmpty(), // First image is primary if no images exist
                    'sort_order' => $product->images->count() + $index
                ]);
            }
        }

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