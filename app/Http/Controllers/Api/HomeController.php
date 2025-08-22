<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shop;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            // Get coordinates from query parameters or request body
            $latitude = $request->query('latitude') ?? $request->input('latitude') ?? $request->json('latitude');
            $longitude = $request->query('longitude') ?? $request->input('longitude') ?? $request->json('longitude');

            // Get authenticated user using Sanctum
            $user = null;
            if ($token = $request->bearerToken()) {
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($personalAccessToken) {
                    $user = $personalAccessToken->tokenable;
                }
            }

            // Validate coordinates
            if ($latitude && $longitude) {
                $validator = Validator::make([
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ], [
                    'latitude' => 'required|numeric|between:-90,90',
                    'longitude' => 'required|numeric|between:-180,180'
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid coordinates',
                        'errors' => $validator->errors()
                    ], 422);
                }
            }

            // Get nearest address if coordinates are provided
            $nearestAddress = null;
            if ($latitude && $longitude && $user) {
                // Find nearest address within 10km radius
                $nearestAddress = $user->addresses()
                    ->selectRaw("
                        *,
                        (6371 * acos(
                            cos(radians(?)) * 
                            cos(radians(latitude)) * 
                            cos(radians(longitude) - radians(?)) + 
                            sin(radians(?)) * 
                            sin(radians(latitude))
                        )) AS distance", [$latitude, $longitude, $latitude])
                    ->having('distance', '<=', 10)
                    ->orderBy('distance')
                    ->first();

                if ($nearestAddress) {
                    $nearestAddress = [
                        'id' => $nearestAddress->id,
                        'name' => $nearestAddress->name,
                        'phone' => $nearestAddress->phone,
                        'address_line1' => $nearestAddress->address_line1,
                        'address_line2' => $nearestAddress->address_line2,
                        'city' => $nearestAddress->city,
                        'state' => $nearestAddress->state,
                        'country' => $nearestAddress->country,
                        'postal_code' => $nearestAddress->postal_code,
                        'is_default' => $nearestAddress->is_default,
                        'address_type' => $nearestAddress->address_type,
                        'landmark' => $nearestAddress->landmark,
                        'distance' => round($nearestAddress->distance, 1) . ' km'
                    ];
                }
            }

            // Get featured products with active variants
            $goToItems = Product::with(['variants' => function($q) {
                $q->where('is_active', true)
                  ->where('stock', '>', 0);
            }])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(10)
            ->get()
            ->map(function($product) {
                $variant = $product->variants->first(); // Get first active variant
                
                if (!$variant) return null;

                // Calculate discounted price using product-level discount
                $discountedPrice = $product->discount > 0 
                    ? $variant->price * (1 - $product->discount / 100)
                    : $variant->price;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image_url' => $product->image_url,
                    'unit' => $variant->unit,
                    'quantity' => $variant->quantity,
                    'original_price' => $variant->price,
                    'discounted_price' => $discountedPrice,
                    'discount_percentage' => $product->discount ?? 0,
                    'variant_id' => $variant->id
                ];
            })
            ->filter() // Remove null values
            ->values(); // Reset array keys

            // Get nearest shops if location provided
            $nearestShops = [];
            if ($latitude && $longitude) {
                $nearestShops = Shop::select([
                    'shops.*',
                    \DB::raw('(
                        6371 * acos(
                            cos(radians(' . $latitude . ')) 
                            * cos(radians(latitude)) 
                            * cos(radians(longitude) - radians(' . $longitude . ')) 
                            + sin(radians(' . $latitude . ')) 
                            * sin(radians(latitude))
                        )
                    ) as distance')
                ])
                ->where('is_active', true)
                ->where('is_verified', true)
                ->having('distance', '<=', 10) // Within 10km
                ->orderBy('distance')
                ->take(5)
                ->get()
                ->map(function($shop) {
                    return [
                        'id' => $shop->id,
                        'name' => $shop->name,
                        'image_url' => $shop->image_path,
                        'address' => $shop->address,
                        'distance' => round($shop->distance, 1) . ' km'
                    ];
                });
            }

            // Get categories with their icons
            $categories = Category::where('is_active', true)
                ->orderBy('display_order')
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'icon_url' => $category->icon_url,
                        'image_url' => $category->image_url
                    ];
                });

            // Get deal countdown time
            $now = Carbon::now();
            $dealEndsAt = Carbon::now()->addDays(2)->endOfDay();

            // Only calculate differences if deal hasn't ended
            if ($now->lt($dealEndsAt)) {
                $diffInSeconds = $now->diffInSeconds($dealEndsAt);
                
                $timeLeft = [
                    'days' => (int)($diffInSeconds / (24 * 60 * 60)), // Convert to whole days
                    'hours' => (int)(($diffInSeconds % (24 * 60 * 60)) / (60 * 60)), // Remaining hours
                    'minutes' => (int)(($diffInSeconds % (60 * 60)) / 60), // Remaining minutes
                    'seconds' => $diffInSeconds % 60, // Remaining seconds
                    'endsat' => $dealEndsAt->toISOString()
                ];
            } else {
                $timeLeft = [
                    'days' => 0,
                    'hours' => 0,
                    'minutes' => 0,
                    'seconds' => 0,
                    'endsat' => null
                ];
            }

            // Use categories for main categories instead of hardcoding
            $mainCategories = $categories->take(2); // Take first 2 categories

            $mainBanners = Banner::where('section', Banner::SECTION_MOBILE_APP_MAIN)
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get()
                ->map(function ($banner) {
                    return [
                        'id' => $banner->id,
                        'title' => $banner->title,
                        'subtitle' => $banner->subtitle,
                        'description' => $banner->description,
                        'image_url' => asset($banner->image_url),
                        'button_text' => $banner->button_text,
                        'button_url' => $banner->button_url,
                    ];
                });

            $bottomBanners = Banner::where('section', Banner::SECTION_MOBILE_APP_BOTTOM)
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get()
                ->map(function ($banner) {
                    return [
                        'id' => $banner->id,
                        'title' => $banner->title,
                        'subtitle' => $banner->subtitle,
                        'description' => $banner->description,
                        'image_url' => asset($banner->image_url),
                        'button_text' => $banner->button_text,
                        'button_url' => $banner->button_url,
                    ];
                });

            $featuredProducts = Product::where('is_active', true)
                ->where('is_featured', true)
                ->with(['category', 'images'])
                ->take(10)
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'deal_countdown' => $timeLeft,
                    'go_to_items' => $goToItems,
                    'nearest_shops' => $nearestShops,
                    'nearest_address' => $nearestAddress,
                    'categories' => $categories,
                    'main_categories' => $mainCategories,
                    'banner' => $mainBanners,
                    'bottom_banners' => $bottomBanners,
                    'featured_products' => $featuredProducts
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch home page data'
            ], 500);
        }
    }
} 