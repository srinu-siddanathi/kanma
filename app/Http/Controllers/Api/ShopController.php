<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::with(['branch', 'owner'])
            ->where('is_active', true)
            ->where('is_verified', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $shops
        ]);
    }

    public function show(Shop $shop)
    {
        if (!$shop->is_active || !$shop->is_verified) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not found or inactive'
            ], 404);
        }

        $shop->load(['branch', 'owner']);
        $shop->loadCount(['products' => function ($query) {
            $query->where('is_active', true);
        }]);

        return response()->json([
            'status' => 'success',
            'data' => $shop
        ]);
    }

    public function categories(Shop $shop)
    {
        if (!$shop->is_active || !$shop->is_verified) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not found or inactive'
            ], 404);
        }

        $categories = Category::whereHas('products', function ($query) use ($shop) {
            $query->where('shop_id', $shop->id)
                ->where('is_active', true);
        })
        ->withCount(['products' => function ($query) use ($shop) {
            $query->where('shop_id', $shop->id)
                ->where('is_active', true);
        }])
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function products(Shop $shop, Category $category)
    {
        if (!$shop->is_active || !$shop->is_verified) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not found or inactive'
            ], 404);
        }

        $products = Product::where('shop_id', $shop->id)
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->with(['category', 'images'])
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function search(Shop $shop, Request $request)
    {
        if (!$shop->is_active || !$shop->is_verified) {
            return response()->json([
                'status' => 'error',
                'message' => 'Shop not found or inactive'
            ], 404);
        }

        $request->validate([
            'query' => 'required|string|min:2',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        $searchQuery = $request->input('query');

        $query = Product::where('shop_id', $shop->id)
            ->where('is_active', true)
            ->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('description', 'like', '%' . $searchQuery . '%');
            });

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->with(['category', 'images'])
            ->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|numeric|min:1|max:50' // radius in kilometers
        ]);

        $radius = $request->radius ?? 10; // default 10km radius

        $shops = Shop::select(
            'shops.*',
            DB::raw("
                6371 * acos(
                    cos(radians({$request->latitude})) * 
                    cos(radians(latitude)) * 
                    cos(radians(longitude) - radians({$request->longitude})) + 
                    sin(radians({$request->latitude})) * 
                    sin(radians(latitude))
                ) AS distance
            ")
        )
        ->having('distance', '<=', $radius)
        ->where('is_active', true)
        ->where('is_verified', true)
        ->with(['branch', 'owner'])
        ->withCount(['products' => function ($query) {
            $query->where('is_active', true);
        }])
        ->orderBy('distance')
        ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $shops
        ]);
    }
} 