<?php

namespace App\Models;

use App\Concerns\PublishesWebsiteContent;
use Database\Factories\FaqFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property array<string, mixed> $draft
 * @property array<string, mixed>|null $published
 * @property Carbon|null $published_at
 */
#[Fillable(['draft'])]
class Faq extends Model
{
    /** @use HasFactory<FaqFactory> */
    use HasFactory, PublishesWebsiteContent;

    /** @return array<string, mixed> */
    public function publicationRules(): array
    {
        return [
            'question.en' => 'required|string|max:300', 'question.ar' => 'required|string|max:300',
            'answer.en' => 'required|string|max:4000', 'answer.ar' => 'required|string|max:4000',
            'sort_order' => 'required|integer|min:0|max:10000',
            'facts_approved' => 'accepted', 'arabic_reviewed' => 'accepted',
        ];
    }
}
