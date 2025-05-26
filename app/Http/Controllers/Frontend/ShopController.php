<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get all active categories with their product counts
            $categories = Category::where('is_active', true)
                ->withCount(['products' => function ($query) {
                    $query->where('is_active', true)
                        ->whereNull('shop_id'); // Only count main products
                }])
                ->get();

            // Get the total product count (not filtered)
            $totalProductCount = Product::where('is_active', true)
                ->whereNull('shop_id')
                ->count();

            // Start building the product query
            $query = Product::with(['category', 'variants' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->where('is_active', true)
                ->whereNull('shop_id'); // Only show main products

            // Apply category filter if provided
            if ($request->has('category')) {
                $category = Category::where('slug', $request->category)->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }

            // Apply price range filter if provided
            if ($request->has('min_price') || $request->has('max_price')) {
                $minPrice = $request->min_price ?? 0;
                $maxPrice = $request->max_price ?? PHP_FLOAT_MAX;
                
                $query->whereHas('variants', function ($q) use ($minPrice, $maxPrice) {
                    $q->where('is_active', true)
                        ->whereBetween('price', [$minPrice, $maxPrice]);
                });
            }

            // Apply sorting
            switch ($request->sort) {
                case 'popular':
                    $query->orderBy('views', 'desc');
                    break;
                case 'latest':
                    $query->latest();
                    break;
                case 'price_low_high':
                    $query->whereHas('variants', function ($q) {
                        $q->where('is_active', true);
                    })->orderBy(function ($query) {
                        $query->select('price')
                            ->from('product_variants')
                            ->whereColumn('product_id', 'products.id')
                            ->where('is_active', true)
                            ->orderBy('price', 'asc')
                            ->limit(1);
                    }, 'asc');
                    break;
                case 'price_high_low':
                    $query->whereHas('variants', function ($q) {
                        $q->where('is_active', true);
                    })->orderBy(function ($query) {
                        $query->select('price')
                            ->from('product_variants')
                            ->whereColumn('product_id', 'products.id')
                            ->where('is_active', true)
                            ->orderBy('price', 'desc')
                            ->limit(1);
                    }, 'desc');
                    break;
                default:
                    $query->latest();
            }

            // Fetch featured products (change logic if you want a different criteria)
            $featuredProducts = Product::with(['variants' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->where('is_active', true)
                ->whereNull('shop_id')
                ->where(function($q) {
                    $q->where('is_featured', true);
                })
                ->take(3)
                ->get();

            // Get paginated products
            $products = $query->paginate(12);

            return view('shop', compact('products', 'categories', 'featuredProducts', 'totalProductCount'));
        } catch (\Exception $e) {
            \Log::error('Shop page error: ' . $e->getMessage());
            
            // Return an empty paginated collection
            $products = new LengthAwarePaginator(
                collect(),
                0,
                12,
                $request->get('page', 1)
            );
            
            return view('shop', [
                'products' => $products,
                'categories' => collect(),
                'error' => 'An error occurred while loading the shop page.'
            ]);
        }
    }
} 