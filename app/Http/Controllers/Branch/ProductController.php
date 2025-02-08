<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\HasImageUpload;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    use HasImageUpload;

    public function index()
    {
        $branch = auth()->user()->branch;
        $products = $branch->products()
            ->select('products.*')
            ->with(['category', 'subcategory', 'branch'])
            ->withPivot('price', 'is_active')
            ->orderBy('products.created_at', 'desc')
            ->paginate(10);

        // Debug log
        \Log::info('Products query:', $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'branch_id' => $product->branch_id
            ];
        })->toArray());

        return view('branch.products.index', compact('products'));
    }

    public function available()
    {
        $branch = auth()->user()->branch;
        $availableProducts = Product::whereDoesntHave('branches', function($query) use ($branch) {
            $query->where('branch_id', $branch->id);
        })->with(['category', 'subcategory'])->paginate(10);

        return view('branch.products.available', compact('availableProducts'));
    }

    public function addToBranch(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0'
        ]);

        $branch = auth()->user()->branch;
        $product = Product::findOrFail($validated['product_id']);

        $branch->products()->attach($product->id, [
            'price' => $validated['price'],
            'is_active' => true
        ]);

        return redirect()
            ->route('branch.products.index')
            ->with('success', 'Product added successfully');
    }

    public function updatePrice(Request $request, Product $product)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0'
        ]);

        $branch = auth()->user()->branch;
        
        // Check if this branch has access to this product
        if (!$branch->products()->where('product_id', $product->id)->exists()) {
            abort(403, 'You can only update prices for products in your branch.');
        }
        
        $branch->products()->updateExistingPivot($product->id, [
            'price' => $validated['price']
        ]);

        return redirect()
            ->back()
            ->with('success', 'Price updated successfully');
    }

    public function toggleStatus(Product $product)
    {
        $branch = auth()->user()->branch;
        
        // Check if this branch has access to this product
        if (!$branch->products()->where('product_id', $product->id)->exists()) {
            abort(403, 'You can only toggle status for products in your branch.');
        }

        $currentStatus = $branch->products()->where('product_id', $product->id)->first()->pivot->is_active;

        $branch->products()->updateExistingPivot($product->id, [
            'is_active' => !$currentStatus
        ]);

        return redirect()
            ->back()
            ->with('success', 'Product status updated successfully');
    }

    public function create()
    {
        $categories = Category::with('subcategories')->get();
        return view('branch.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $branch = auth()->user()->branch;

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadImage($request->file('image'));
        }

        // Debug log
        \Log::info('Creating product with branch_id: ' . $branch->id);

        // Create the product
        $product = Product::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'image_path' => $imagePath,
            'is_active' => true,
            'branch_id' => $branch->id
        ]);

        // Debug log
        \Log::info('Created product with branch_id: ' . $product->branch_id);

        // Add to branch_products pivot
        $branch->products()->attach($product->id, [
            'price' => $validated['price'],
            'is_active' => true
        ]);

        return redirect()
            ->route('branch.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        // Check if product belongs to this branch
        $branch = auth()->user()->branch;
        if ($product->branch_id !== $branch->id) {
            abort(403, 'You can only edit products created by your branch.');
        }

        $categories = Category::with('subcategories')->get();
        return view('branch.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        // Check if product belongs to this branch
        $branch = auth()->user()->branch;
        if ($product->branch_id !== $branch->id) {
            abort(403, 'You can only update products created by your branch.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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
            'image_path' => $imagePath
        ]);

        // Update branch_products pivot price
        $branch->products()->updateExistingPivot($product->id, [
            'price' => $validated['price']
        ]);

        return redirect()
            ->route('branch.products.index')
            ->with('success', 'Product updated successfully');
    }
} 