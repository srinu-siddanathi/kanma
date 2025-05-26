<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Banner::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $banners = [
            [
                'title' => 'Special Offer: 30% Off Fresh Fruits',
                'subtitle' => 'Limited Time Offer',
                'description' => 'Get amazing discounts on all fresh fruits',
                'image_url' => 'images/banners/banner1.jpg',
                'button_text' => 'Shop Now',
                'button_url' => '/category/fresh-fruits',
                'is_active' => true,
                'display_order' => 1
            ],
            [
                'title' => 'Organic Vegetables Fresh from Farm',
                'subtitle' => 'Farm Fresh',
                'description' => 'Handpicked fresh vegetables delivered to your doorstep',
                'image_url' => 'images/banners/banner2.jpg',
                'button_text' => 'Shop Now',
                'button_url' => '/category/fresh-vegetables',
                'is_active' => true,
                'display_order' => 2
            ],
            [
                'title' => 'New Arrivals: Dairy Products',
                'subtitle' => 'Fresh Daily',
                'description' => 'Fresh dairy products from local farms',
                'image_url' => 'images/banners/banner3.jpg',
                'button_text' => 'Shop Now',
                'button_url' => '/category/dairy-products',
                'is_active' => true,
                'display_order' => 3
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
} 