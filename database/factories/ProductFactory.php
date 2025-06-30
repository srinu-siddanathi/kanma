<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'category_id' => Category::factory(),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'base_unit' => $this->faker->randomElement(['1kg', '500g', '1l', '500ml', '1pc']),
            'image_path' => null,
            'is_active' => true,
            'is_verified' => true,
            'status' => 'verified',
            'rejection_reason' => null,
            'shop_id' => null,
            'is_deal' => false,
            'is_featured' => false,
            'discount' => 0,
            'deal_end_date' => null,
        ];
    }

    /**
     * Indicate that the product is a deal.
     */
    public function deal(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_deal' => true,
            'discount' => $this->faker->numberBetween(10, 50),
            'deal_end_date' => $this->faker->dateTimeBetween('now', '+30 days'),
        ]);
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
} 