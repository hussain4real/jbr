<?php

namespace Database\Factories;

use App\Models\MediaAsset;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MediaAsset> */
class MediaAssetFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['original_path' => 'website/originals/'.fake()->uuid().'.png', 'draft' => [
            'alt' => ['en' => 'Test image', 'ar' => 'صورة تجريبية'], 'caption' => ['en' => 'Test caption', 'ar' => 'تعليق تجريبي'],
            'sort_order' => 0, 'focal_x' => 50, 'focal_y' => 50, 'rights_confirmed' => true, 'property_verified' => true, 'arabic_reviewed' => true,
        ]];
    }

    public function published(): static
    {
        return $this->afterCreating(fn (MediaAsset $record) => $record->publish());
    }

    public function ready(): static
    {
        return $this->state(fn (): array => ['processing_status' => 'ready', 'variants' => [480 => ['path' => 'website/variants/test/480.webp', 'width' => 480, 'height' => 360], 960 => ['path' => 'website/variants/test/960.webp', 'width' => 960, 'height' => 720], 1600 => ['path' => 'website/variants/test/1600.webp', 'width' => 1600, 'height' => 1200]]]);
    }
}
