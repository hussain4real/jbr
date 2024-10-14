<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'price' => $this->faker->randomFloat(2, 1000, 100000),
            'type' => $this->faker->randomElement(['apartment', 'house', 'villa']),
            'bedrooms' => $this->faker->numberBetween(1, 5),
            'bathrooms' => $this->faker->numberBetween(1, 5),
            'area' => $this->faker->numberBetween(500, 1500),
            'parking' => $this->faker->randomElement(['unavailable', 'garage', 'available']),
            'swimming_pool_type' => $this->faker->randomElement(['none', 'private', 'shared']),
            'garden_type' => $this->faker->randomElement(['none', 'private', 'shared']),
            'amenities' => $this->faker->randomElements(['wifi', 'kitchen', 'tv', 'heating', 'ac'], $this->faker->numberBetween(1, 5)),
            'is_featured' => $this->faker->boolean(20),
            'status' => $this->faker->randomElement(['available', 'reserved', 'unavailable','under_maintenance']),
        ];
    }
}
