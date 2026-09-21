<?php

namespace Database\Factories;

use App\Models\Accommodation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Accommodation> */
class AccommodationFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['slug' => fake()->unique()->slug(), 'draft' => [
            'title' => ['en' => 'Test accommodation', 'ar' => 'إقامة تجريبية'], 'description' => ['en' => 'Test description', 'ar' => 'وصف تجريبي'],
            'inclusions' => ['en' => 'Test inclusions', 'ar' => 'تفاصيل تجريبية'], 'capacity' => 2, 'sort_order' => 0,
            'media_ids' => [], 'facts_approved' => true, 'arabic_reviewed' => true,
        ]];
    }

    public function published(): static
    {
        return $this->afterCreating(fn (Accommodation $record) => $record->publish());
    }
}
