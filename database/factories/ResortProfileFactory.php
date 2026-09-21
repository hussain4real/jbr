<?php

namespace Database\Factories;

use App\Models\ResortProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ResortProfile> */
class ResortProfileFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['key' => 'main', 'draft' => [
            'introduction' => ['en' => 'Test resort introduction', 'ar' => 'مقدمة تجريبية للمنتجع'],
            'privacy' => ['en' => 'Test privacy notice', 'ar' => 'إشعار خصوصية تجريبي'],
            'phone' => '+96890000000', 'email' => 'resort@example.invalid',
            'contacts_verified' => true, 'requests_enabled' => true, 'privacy_approved' => true, 'operations_approved' => true, 'arabic_reviewed' => true, 'whatsapp_verified' => false, 'retention_days' => 30,
        ]];
    }

    public function published(): static
    {
        return $this->afterCreating(fn (ResortProfile $record) => $record->publish());
    }
}
