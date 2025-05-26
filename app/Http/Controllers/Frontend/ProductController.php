<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function show($id)
    {
        try {
            $product = Product::with(['category', 'subcategory', 'shop'])
                ->findOrFail($id);
            
            // Fetch similar products (same category, exclude current)
            $similarProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->limit(8)
                ->get();
            
            // Log the product data for debugging
            Log::info('Product data:', [
                'id' => $product->id,
                'name' => $product->name,
                'has_category' => $product->category ? true : false,
                'has_shop' => $product->shop ? true : false
            ]);
            
            // Only redirect if the product is not active
            if (!$product->is_active) {
                return redirect()->route('shop')->with('error', 'This product is not available.');
            }
            
            return view('frontend.products.show', compact('product', 'similarProducts'));
        } catch (\Exception $e) {
            Log::error('Product view error:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('shop')->with('error', 'Product not found.');
        }
    }
} 