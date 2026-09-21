<?php

namespace Database\Factories;

use App\Models\GuestRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<GuestRequest> */
class GuestRequestFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['reference' => (string) Str::uuid(), 'submission_hash' => hash('sha256', (string) Str::uuid()), 'kind' => 'enquiry', 'locale' => 'en', 'name' => fake()->name(), 'email' => fake()->safeEmail(), 'phone' => '+96890000000', 'message' => 'Test enquiry', 'status' => 'new'];
    }
}
