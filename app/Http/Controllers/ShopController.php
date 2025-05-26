<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'variants' => function($query) {
                $query->where('is_active', true);
            }])
            ->where('is_active', true)
            ->whereNull('shop_id'); // Only show main products, not shop products

            // Apply category filter
            if ($request->has('category')) {
                $category = Category::find($request->category);
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }

            // Apply price range filter
            if ($request->has('min_price') && $request->has('max_price')) {
                $query->whereHas('variants', function($q) use ($request) {
                    $q->whereBetween('price', [$request->min_price, $request->max_price]);
                });
            }

            // Apply sorting
            switch ($request->sort) {
                case 'popularity':
                    $query->orderBy('sales_count', 'desc');
                    break;
                case 'latest':
                    $query->latest();
                    break;
                case 'price_low_high':
                    $query->whereHas('variants', function($q) {
                        $q->orderBy('price', 'asc');
                    });
                    break;
                case 'price_high_low':
                    $query->whereHas('variants', function($q) {
                        $q->orderBy('price', 'desc');
                    });
                    break;
                default:
                    $query->latest();
            }

            // Get categories for sidebar
            $categories = Category::where('is_active', true)
                ->withCount(['products' => function($query) {
                    $query->where('is_active', true)
                          ->whereNull('shop_id');
                }])
                ->get();

            // Get products with pagination
            $products = $query->paginate(12);

            return view('shop', compact('products', 'categories'));
        } catch (\Exception $e) {
            \Log::error('Shop page error: ' . $e->getMessage());
            return view('shop', [
                'products' => collect(),
                'categories' => collect(),
                'error' => 'An error occurred while loading the shop page.'
            ]);
        }
    }
} 