<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function nearby(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'nullable|numeric|min:0|max:50', // radius in kilometers
                'limit' => 'nullable|integer|min:1|max:50'
            ]);

            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->radius ?? 10; // Default 10km
            $limit = $request->limit ?? 20; // Default 20 shops

            // Haversine formula to calculate distances
            $shops = Shop::select([
                'shops.*',
                DB::raw('(
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
            ->where('approval_status', 'approved')
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->limit($limit)
            ->get();

            // Transform the response
            $transformedShops = $shops->map(function ($shop) {
                return [
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'description' => $shop->description,
                    'address' => $shop->address,
                    'phone' => $shop->phone,
                    'email' => $shop->email,
                    'image_url' => $shop->image_url,
                    'latitude' => $shop->latitude,
                    'longitude' => $shop->longitude,
                    'distance' => round($shop->distance, 2), // Distance in kilometers
                    'is_open' => $shop->is_open, // You'll need to add this logic
                    'rating' => $shop->rating, // You'll need to add this if you have ratings
                    'total_reviews' => $shop->reviews_count // You'll need to add this if you have reviews
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'shops' => $transformedShops
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Nearby shops error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch nearby shops'
            ], 500);
        }
    }
} 