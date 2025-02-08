<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * List products with optional branch filter
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::query()
                ->with(['category', 'subcategory', 'branches']);

            // Filter by branch if branch_id is provided
            if ($request->has('branch_id')) {
                $branchId = $request->branch_id;
                
                // Join with branch_products table
                $query->join('branch_products', function($join) use ($branchId) {
                    $join->on('products.id', '=', 'branch_products.product_id')
                         ->where('branch_products.branch_id', '=', $branchId)
                         ->where('branch_products.is_active', '=', true);
                });

                // Select specific fields including branch-specific price
                $query->select([
                    'products.*',
                    'branch_products.price as branch_price',
                    'branch_products.is_active as branch_is_active'
                ]);
            }

            // Additional filters
            if ($request->has('category_id')) {
                $query->where('products.category_id', $request->category_id);
            }

            if ($request->has('subcategory_id')) {
                $query->where('products.subcategory_id', $request->subcategory_id);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('products.name', 'like', "%{$search}%")
                      ->orWhere('products.description', 'like', "%{$search}%");
                });
            }

            // Only get active products
            $query->where('products.is_active', true);

            // Get distinct products to avoid duplicates
            $query->distinct('products.id');

            $products = $query->latest()->get();

            // Transform the response
            $products = $products->map(function ($product) use ($request) {
                $data = $product->toArray();
                if ($request->has('branch_id')) {
                    $data['price'] = $product->branch_price ?? $product->price;
                    $data['is_active'] = $product->branch_is_active;
                }
                return $data;
            });

            return response()->json([
                'status' => 'success',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            // Add error logging for debugging
            \Log::error('Product listing error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch products',
                'debug_message' => $e->getMessage() // Remove in production
            ], 500);
        }
    }

    /**
     * Get product details
     */
    public function show(Product $product): JsonResponse
    {
        if (!$product->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        $product->load(['category', 'subcategory', 'branches']);

        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }
} 