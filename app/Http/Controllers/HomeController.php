<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Blog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // Get banners
            $banners = Banner::where('is_active', true)
                ->orderBy('display_order')
                ->take(3)
                ->get();

            if ($banners->isEmpty()) {
                Log::info('No active banners found');
            }

            // Get categories
            $categories = Category::where('is_active', true)
                ->orderBy('display_order')
                ->get();

            if ($categories->isEmpty()) {
                Log::info('No active categories found');
            }

            // Get trending products (excluding shop products)
            $trendingProducts = Product::with(['variants', 'category'])
                ->where('is_active', true)
                ->where('is_trending', true)
                ->whereNull('shop_id')
                ->take(8)
                ->get();

            // Get best selling products (excluding shop products)
            $bestSelling = Product::with(['variants'])
                ->where('is_active', true)
                ->whereNull('shop_id')
                ->orderBy('sales_count', 'desc')
                ->take(8)
                ->get();

            // Get latest products (excluding shop products)
            $justArrived = Product::with(['variants'])
                ->where('is_active', true)
                ->whereNull('shop_id')
                ->latest()
                ->take(8)
                ->get();

            // Get featured brands
            // $brands = Brand::where('is_active', true)
            //     ->orderBy('display_order')
            //     ->take(10)
            //     ->get();

            // Get latest blog posts
            // $blogPosts = Blog::where('is_active', true)
            //     ->latest()
            //     ->take(3)
            //     ->get();

            // Get deal countdown
            $timeLeft = [
                'days' => 2,
                'hours' => 0,
                'minutes' => 0,
                'seconds' => 0,
                'endsat' => now()->addDays(2)->toISOString()
            ];

            $data = (object)[
                'banners' => $banners,
                'categories' => $categories,
                'trending_products' => $trendingProducts,
                'best_selling' => $bestSelling,
                'just_arrived' => $justArrived,
                // 'brands' => $brands,
                // 'blog_posts' => $blogPosts,
                'deal_countdown' => $timeLeft,
                'has_countdown' => true
            ];

            return view('home', compact('data'));

        } catch (\Exception $e) {
            echo $e->getMessage();
            Log::error('Home page error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return view('home')->with('error', 'Failed to load home page data');
        }
    }
} 