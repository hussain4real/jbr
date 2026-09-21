<?php

namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Faq> */
class FaqFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return ['draft' => ['question' => ['en' => 'Test question?', 'ar' => 'سؤال تجريبي؟'], 'answer' => ['en' => 'Test answer.', 'ar' => 'إجابة تجريبية.'], 'sort_order' => 0, 'facts_approved' => true, 'arabic_reviewed' => true]];
    }

    public function published(): static
    {
        return $this->afterCreating(fn (Faq $record) => $record->publish());
    }
}
