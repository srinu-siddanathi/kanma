<?php

namespace App\Http\Controllers\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
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
            ->with(['category', 'subcategory', 'images'])
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
            'selected_images' => 'nullable|array',
            'selected_images.*' => 'nullable|string',
        ]);

        $validated['shop_id'] = auth()->user()->shop->id;
        $validated['slug'] = $this->generateUniqueSlug($validated['name']);

        // Handle new image upload
        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/products'), $filename);
            $validated['image_path'] = 'uploads/products/' . $filename;
        }

        $product = Product::create($validated);

        // Handle selected existing images
        if ($request->has('selected_images') && is_array($request->input('selected_images')) && !empty($request->input('selected_images'))) {
            foreach ($request->input('selected_images') as $imagePath) {
                if (!empty($imagePath)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'is_primary' => false,
                        'sort_order' => ProductImage::where('product_id', $product->id)->count()
                    ]);
                }
            }
        }

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
            'selected_images' => 'nullable|array',
            'selected_images.*' => 'nullable|string',
        ]);

        // Generate new slug if name has changed
        if ($product->name !== $validated['name']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['name']);
        }

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

        // Handle selected existing images
        if ($request->has('selected_images') && is_array($request->input('selected_images')) && !empty($request->input('selected_images'))) {
            // Remove existing product images
            $product->images()->delete();
            
            // Create new product images from selected images
            foreach ($request->input('selected_images') as $imagePath) {
                if (!empty($imagePath)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $imagePath,
                        'is_primary' => false,
                        'sort_order' => ProductImage::where('product_id', $product->id)->count()
                    ]);
                }
            }
        }

        return redirect()
            ->route('shop-owner.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        if ($product->shop_id !== auth()->user()->shop->id) {
            abort(403, 'You are not authorized to delete this product.');
        }

        try {
            // Start a database transaction
            \DB::beginTransaction();
            
            // Delete related order items first (since they don't have cascade delete)
            $product->orderItems()->delete();
            
            // Delete product images (these have cascade delete, but being explicit)
            $product->images()->delete();
            
            // Delete product variants (these have cascade delete, but being explicit)
            $product->variants()->delete();
            
            // Detach from branches
            $product->branches()->detach();
            
            // Delete the product
            $product->delete();
            
            // Commit the transaction
            \DB::commit();
            
            return redirect()
                ->route('shop-owner.products.index')
                ->with('success', 'Product deleted successfully');
                
        } catch (\Exception $e) {
            // Rollback the transaction on error
            \DB::rollBack();
            
            \Log::error('Error deleting product:', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->route('shop-owner.products.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function searchImages(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $query = $request->input('query');
        $shopId = auth()->user()->shop->id;

        // Split the query into individual words for more flexible searching
        $searchTerms = array_filter(explode(' ', strtolower($query)));
        
        // Search for products with similar names and get their images
        $products = Product::where('shop_id', $shopId)
            ->where(function($q) use ($searchTerms, $query) {
                // Search for exact phrase match
                $q->where('name', 'like', '%' . $query . '%');
                
                // Search for individual word matches
                foreach ($searchTerms as $term) {
                    if (strlen($term) >= 2) { // Only search for terms with 2+ characters
                        $q->orWhere('name', 'like', '%' . $term . '%');
                    }
                }
            })
            ->with('images')
            ->get();

        $images = collect();
        
        foreach ($products as $product) {
            // Add main product image if exists
            if ($product->image_path) {
                $images->push([
                    'id' => 'main_' . $product->id,
                    'path' => $product->image_path,
                    'url' => asset($product->image_path),
                    'product_name' => $product->name,
                    'type' => 'main'
                ]);
            }
            
            // Add product images
            foreach ($product->images as $image) {
                $images->push([
                    'id' => 'image_' . $image->id,
                    'path' => $image->image_path,
                    'url' => asset($image->image_path),
                    'product_name' => $product->name,
                    'type' => 'additional'
                ]);
            }
        }

        // Also search in the uploads directory for images with similar names
        $uploadPath = public_path('uploads/products');
        if (is_dir($uploadPath)) {
            $files = scandir($uploadPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($uploadPath . '/' . $file)) {
                    $fileName = pathinfo($file, PATHINFO_FILENAME);
                    $fileNameLower = strtolower($fileName);
                    
                    // Check if any search term matches the filename
                    $matches = false;
                    foreach ($searchTerms as $term) {
                        if (strlen($term) >= 2 && stripos($fileNameLower, $term) !== false) {
                            $matches = true;
                            break;
                        }
                    }
                    
                    // Also check for exact phrase match
                    if (!$matches && stripos($fileNameLower, strtolower($query)) !== false) {
                        $matches = true;
                    }
                    
                    if ($matches) {
                        $imagePath = 'uploads/products/' . $file;
                        $images->push([
                            'id' => 'file_' . $file,
                            'path' => $imagePath,
                            'url' => asset($imagePath),
                            'product_name' => 'Uploaded Image',
                            'type' => 'uploaded'
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'images' => $images->unique('path')->values()
        ]);
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = 1;
        $originalSlug = $slug;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
} 