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

                // Handle images: check products table first, then product_images table
                $images = [];
                
                // First check if product has image_path in products table
                if ($product->image_path) {
                    $images[] = [
                        'id' => null,
                        'url' => asset($product->image_path),
                        'is_primary' => true
                    ];
                }
                
                // Then check product_images table
                if ($product->images->isNotEmpty()) {
                    foreach ($product->images as $image) {
                        $images[] = [
                            'id' => $image->id,
                            'url' => asset($image->image_path),
                            'is_primary' => $image->is_primary
                        ];
                    }
                }
                
                // If no images found, use default
                if (empty($images)) {
                    $images[] = [
                        'id' => null,
                        'url' => asset('images/no-image.png'),
                        'is_primary' => true
                    ];
                }

                $data = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'images' => $images,
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
     * Get deal products with deal end dates
     */
    public function deals(Request $request): JsonResponse
    {
        try {
            $query = Product::with(['category', 'images'])
                ->where('is_active', true)
                ->where('is_deal', true)
                ->whereNotNull('deal_end_date')
                ->where('deal_end_date', '>', now()); // Only active deals

            // Apply filters
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Sort by deal end date (earliest first)
            $query->orderBy('deal_end_date', 'asc');

            $products = $query->paginate(20);

            $transformedProducts = $products->map(function ($product) {
                $discountedPrice = $product->discount > 0 
                    ? $product->price - ($product->price * ($product->discount / 100))
                    : $product->price;

                // Handle image: check products table first, then product_images table
                $image = null;
                
                // First check if product has image_path in products table
                if ($product->image_path) {
                    $image = asset($product->image_path);
                }
                // Then check product_images table for primary image
                elseif ($product->images->where('is_primary', true)->first()) {
                    $image = asset($product->images->where('is_primary', true)->first()->image_path);
                }
                // Then check for any image in product_images table
                elseif ($product->images->first()) {
                    $image = asset($product->images->first()->image_path);
                }
                // If no images found, use default
                else {
                    $image = asset('images/no-image.png');
                }

                $data = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'image' => $image,
                    'category' => $product->category ? [
                        'id' => $product->category->id,
                        'name' => $product->category->name
                    ] : null,
                    'price' => $product->price,
                    'discounted_price' => round($discountedPrice, 2),
                    'discount_percentage' => $product->discount,
                    'unit' => $product->base_unit,
                    'deal_end_date' => $product->deal_end_date->format('Y-m-d H:i:s'),
                    'deal_end_date_formatted' => $product->deal_end_date->format('M d, Y H:i'),
                    'time_remaining' => [
                        'days' => round(now()->diffInDays($product->deal_end_date, false), 0),
                        'hours' => round(now()->diffInHours($product->deal_end_date, false) % 24, 0),
                        'minutes' => round(now()->diffInMinutes($product->deal_end_date, false) % 60, 0)
                    ],
                    'is_available' => $product->is_active
                ];

                return $data;
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'deals' => $transformedProducts,
                    'pagination' => [
                        'current_page' => $products->currentPage(),
                        'last_page' => $products->lastPage(),
                        'per_page' => $products->perPage(),
                        'total' => $products->total()
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Deal products listing error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch deal products'
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

            // Handle images: check products table first, then product_images table
            $images = [];
            
            // First check if product has image_path in products table
            if ($product->image_path) {
                $images[] = [
                    'id' => null,
                    'url' => asset($product->image_path),
                    'is_primary' => true
                ];
            }
            
            // Then check product_images table
            if ($product->images->isNotEmpty()) {
                foreach ($product->images as $image) {
                    $images[] = [
                        'id' => $image->id,
                        'url' => asset($image->image_path),
                        'is_primary' => $image->is_primary
                    ];
                }
            }
            
            // If no images found, use default
            if (empty($images)) {
                $images[] = [
                    'id' => null,
                    'url' => asset('images/no-image.png'),
                    'is_primary' => true
                ];
            }

            $data = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'images' => $images,
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