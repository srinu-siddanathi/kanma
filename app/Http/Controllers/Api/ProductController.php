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
            $query = Product::with(['category', 'images'])
                ->where('is_active', true);

            // Apply filters
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $products = $query->paginate(20);

            $transformedProducts = $products->map(function ($product) {
                $discountedPrice = $product->discount > 0 
                    ? $product->price - ($product->price * ($product->discount / 100))
                    : $product->price;

                $data = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'images' => $product->images->map(function($image) {
                        return [
                            'id' => $image->id,
                            'url' => $image->image_url,
                            'is_primary' => $image->is_primary
                        ];
                    }),
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name
                    ] : null,
                    'price' => $product->price,
                    'discounted_price' => round($discountedPrice, 2),
                    'unit' => $product->base_unit,
                    'deal' => $product->is_deal,
                    'discount' => $product->discount,
                    'featured' => $product->is_featured,
                    'is_available' => $product->is_active
                ];

                return $data;
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'products' => $transformedProducts,
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Product listing error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch products'
            ], 500);
        }
    }

    /**
     * Get product details
     */
    public function show($id): JsonResponse
    {
        try {
            $product = Product::with(['category', 'images'])
                ->findOrFail($id);

            $discountedPrice = $product->discount > 0 
                ? $product->price - ($product->price * ($product->discount / 100))
                : $product->price;

            $data = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'images' => $product->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->image_url,
                        'is_primary' => $image->is_primary
                    ];
                }),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name
                ] : null,
                'price' => $product->price,
                'discounted_price' => round($discountedPrice, 2),
                'unit' => $product->base_unit,
                'deal' => $product->is_deal,
                'discount' => $product->discount,
                'featured' => $product->is_featured,
                'is_available' => $product->is_active
            ];

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }
    }
} 