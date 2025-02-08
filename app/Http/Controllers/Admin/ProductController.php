<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Traits\HasImageUpload;

class ProductController extends Controller
{
    use HasImageUpload;

    public function index()
    {
        $products = Product::with(['category', 'subcategory', 'branches'])
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
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'branches' => 'array',
            'branches.*' => 'exists:branches,id'
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
        }

        // Create the product
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'image_path' => $imagePath,
            'is_active' => true,
            'branch_id' => null // Set branch_id as null since we're using pivot table
        ]);

        // Attach to selected branches
        if (!empty($validated['branches'])) {
            foreach ($validated['branches'] as $branchId) {
                $product->branches()->attach($branchId, [
                    'price' => $validated['price'],
                    'is_active' => true
                ]);
            }
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully');
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
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'branches' => 'array',
            'branches.*' => 'exists:branches,id'
        ]);

        // Handle image upload
        $imagePath = $product->image_path;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'), 'products', $product->image_path);
        }

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'image_path' => $imagePath,
            'is_active' => $validated['is_active'] ?? true
        ]);

        // Update branch associations
        $product->branches()->sync(
            collect($validated['branches'] ?? [])->mapWithKeys(function ($branchId) use ($validated) {
                return [$branchId => [
                    'price' => $validated['price'],
                    'is_active' => true
                ]];
            })
        );

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
} 