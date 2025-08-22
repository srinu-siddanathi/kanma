<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Branch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Branch',
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'is_active' => true,
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'delivery_radius' => $this->faker->numberBetween(5, 20),
            'minimum_order_amount' => $this->faker->numberBetween(100, 500),
            'delivery_fee' => $this->faker->numberBetween(0, 50),
        ];
    }

    /**
     * Indicate that the branch is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
} 