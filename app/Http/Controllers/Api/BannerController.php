<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function getMobileBanners()
    {
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

        return response()->json([
            'status' => 'success',
            'data' => [
                'main_banners' => $mainBanners,
                'bottom_banners' => $bottomBanners
            ]
        ]);
    }
} 