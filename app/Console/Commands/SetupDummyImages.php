<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupDummyImages extends Command
{
    protected $signature = 'setup:dummy-images';
    protected $description = 'Setup dummy product, category and banner images';

    public function handle()
    {
        $images = [
            'products' => [
                'apples.jpg' => 'https://placehold.co/800x600/FF5733/FFFFFF.jpg?text=Fresh+Organic+Apples',
                'bananas.jpg' => 'https://placehold.co/800x600/FFE633/000000.jpg?text=Organic+Bananas',
                'milk.jpg' => 'https://placehold.co/800x600/FFFFFF/000000.jpg?text=Fresh+Milk',
                'bread.jpg' => 'https://placehold.co/800x600/DEB887/FFFFFF.jpg?text=Whole+Wheat+Bread',
                'honey.jpg' => 'https://placehold.co/800x600/FFA500/FFFFFF.jpg?text=Organic+Honey',
                'eggs.jpg' => 'https://placehold.co/800x600/FAEBD7/000000.jpg?text=Fresh+Eggs',
            ],
            'categories' => [
                'fruits.jpg' => 'https://placehold.co/800x600/FF5733/FFFFFF.jpg?text=Fresh+Fruits',
                'vegetables.jpg' => 'https://placehold.co/800x600/4CAF50/FFFFFF.jpg?text=Fresh+Vegetables',
                'dairy.jpg' => 'https://placehold.co/800x600/2196F3/FFFFFF.jpg?text=Dairy+Products',
                'bakery.jpg' => 'https://placehold.co/800x600/795548/FFFFFF.jpg?text=Bakery+Items',
                'beverages.jpg' => 'https://placehold.co/800x600/9C27B0/FFFFFF.jpg?text=Beverages',
                'snacks.jpg' => 'https://placehold.co/800x600/FF9800/FFFFFF.jpg?text=Snacks',
            ],
            'banners' => [
                'banner1.jpg' => 'https://placehold.co/1920x600/FF9800/FFFFFF.jpg?text=Special+Offer:+30%+Off+Fresh+Fruits',
                'banner2.jpg' => 'https://placehold.co/1920x600/4CAF50/FFFFFF.jpg?text=Organic+Vegetables+Fresh+from+Farm',
                'banner3.jpg' => 'https://placehold.co/1920x600/2196F3/FFFFFF.jpg?text=New+Arrivals:+Dairy+Products',
            ]
        ];

        foreach ($images as $type => $typeImages) {
            $path = public_path("images/{$type}");
            
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true);
            }

            foreach ($typeImages as $filename => $url) {
                try {
                    $this->info("Downloading {$type}/{$filename}...");
                    
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    $contents = curl_exec($ch);
                    
                    if ($contents === false) {
                        $this->error("Failed to download {$filename}: " . curl_error($ch));
                        continue;
                    }
                    
                    curl_close($ch);
                    File::put($path . '/' . $filename, $contents);
                    $this->info("Successfully downloaded {$filename}");
                    
                } catch (\Exception $e) {
                    $this->error("Error downloading {$filename}: " . $e->getMessage());
                }
            }
        }

        $this->info('Image setup completed!');
    }
} 