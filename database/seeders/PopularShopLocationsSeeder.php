<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Traits\HasWorkingHours;

class PopularShopLocationsSeeder extends Seeder
{
    use HasWorkingHours;

    private $locations = [
        [
            'area' => 'Hitech City',
            'lat' => 17.4435,
            'lng' => 78.3772,
        ],
        [
            'area' => 'Gachibowli',
            'lat' => 17.4401,
            'lng' => 78.3489,
        ],
        [
            'area' => 'Jubilee Hills',
            'lat' => 17.4319,
            'lng' => 78.4073,
        ],
        [
            'area' => 'Banjara Hills',
            'lat' => 17.4156,
            'lng' => 78.4347,
        ],
        [
            'area' => 'Kukatpally',
            'lat' => 17.4849,
            'lng' => 78.4138,
        ],
        [
            'area' => 'Madhapur',
            'lat' => 17.4484,
            'lng' => 78.3908,
        ],
        [
            'area' => 'KPHB',
            'lat' => 17.4935,
            'lng' => 78.3931,
        ],
        [
            'area' => 'Ameerpet',
            'lat' => 17.4374,
            'lng' => 78.4487,
        ]
    ];

    public function run(): void
    {
        $shops = Shop::whereNull('latitude')->get();
        $locationCount = count($this->locations);
        
        foreach ($shops as $index => $shop) {
            // Use modulo to cycle through locations if we have more shops than locations
            $location = $this->locations[$index % $locationCount];
            
            // Add small random offset to prevent all shops having exact same coordinates
            $latitude = $location['lat'] + $this->randomOffset();
            $longitude = $location['lng'] + $this->randomOffset();

            $shop->update([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $shop->address ?? 'Near ' . $location['area'] . ', Hyderabad',
                'working_hours' => self::getDefaultWorkingHours(),
                'rating' => $this->randomFloat(3.5, 5.0, 1),
                'reviews_count' => rand(10, 100)
            ]);
        }

        $this->command->info('Shops have been assigned to popular locations!');
    }

    private function randomOffset()
    {
        // Generate offset between -0.002 and 0.002 (roughly 200m)
        return (mt_rand(-200, 200) / 100000);
    }

    private function randomFloat($min, $max, $decimals) 
    {
        $scale = pow(10, $decimals);
        return mt_rand($min * $scale, $max * $scale) / $scale;
    }
} 