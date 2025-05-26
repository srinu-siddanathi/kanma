<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Traits\HasWorkingHours;

class ShopLocationSeeder extends Seeder
{
    use HasWorkingHours;

    // Hyderabad boundaries (approximately)
    private $boundaries = [
        'min_lat' => 17.3850,
        'max_lat' => 17.4950,
        'min_lng' => 78.3350,
        'max_lng' => 78.4750
    ];

    public function run(): void
    {
        $shops = Shop::whereNull('latitude')->get();

        foreach ($shops as $shop) {
            // Generate random location within Hyderabad
            $latitude = $this->randomFloat(
                $this->boundaries['min_lat'],
                $this->boundaries['max_lat'],
                8
            );
            
            $longitude = $this->randomFloat(
                $this->boundaries['min_lng'],
                $this->boundaries['max_lng'],
                8
            );

            // Update shop with location data
            $shop->update([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'working_hours' => self::getDefaultWorkingHours(),
                'rating' => $this->randomFloat(3.5, 5.0, 1), // Random rating between 3.5 and 5.0
                'reviews_count' => rand(10, 100) // Random number of reviews
            ]);
        }

        $this->command->info('Shop locations have been seeded!');
    }

    private function randomFloat($min, $max, $decimals) 
    {
        $scale = pow(10, $decimals);
        return mt_rand($min * $scale, $max * $scale) / $scale;
    }
} 