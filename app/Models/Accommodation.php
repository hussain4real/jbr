<?php

namespace App\Models;

use App\Concerns\PublishesWebsiteContent;
use Database\Factories\AccommodationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property array<string, mixed> $draft
 * @property array<string, mixed>|null $published
 * @property Carbon|null $published_at
 * @property string $slug
 */
#[Fillable(['draft', 'slug'])]
class Accommodation extends Model
{
    /** @use HasFactory<AccommodationFactory> */
    use HasFactory, PublishesWebsiteContent;

    /** @return array<string, mixed> */
    public function publicationRules(): array
    {
        $rules = [
            'title.en' => 'required|string|max:180', 'title.ar' => 'required|string|max:180',
            'description.en' => 'required|string|max:8000', 'description.ar' => 'required|string|max:8000',
            'inclusions.en' => 'required|string|max:4000', 'inclusions.ar' => 'required|string|max:4000',
            'capacity' => 'required|integer|min:1|max:1000', 'sort_order' => 'required|integer|min:0|max:10000',
            'capacity_basis' => ['sometimes', Rule::in(['guests', 'adults'])],
            'media_ids' => 'required|array|min:1|max:20',
            'media_ids.*' => ['integer', Rule::exists('media_assets', 'id')->whereNotNull('published_at')],
            'facts_approved' => 'accepted', 'arabic_reviewed' => 'accepted',
        ];
        if (isset($this->draft['redesign'])) {
            $rules['redesign'] = 'array:page,title,description,overview,inclusions,badge,facts,categories,media_ids';
            $rules['redesign.media_ids'] = 'required|array|min:1|max:20';
            $rules['redesign.media_ids.*'] = ['integer', Rule::exists('media_assets', 'id')->whereNotNull('published_at')];
            $rules['redesign.page'] = ['required', Rule::in(['villa-oman', 'villa-majan', 'villa-salalah', 'villa-mazon', 'villa-luxury-one-bedroom', 'standard-room'])];
            $rules['redesign.categories'] = 'required|array|min:1|max:5';
            $rules['redesign.categories.*'] = [Rule::in(['pool', 'family', 'couples', 'rooms'])];
            foreach (['title', 'description', 'overview', 'inclusions', 'badge', 'facts'] as $field) {
                $rules['redesign.'.$field] = 'required|array:en,ar';
                $rules['redesign.'.$field.'.en'] = $rules['redesign.'.$field.'.ar'] = 'required|string|max:8000';
            }
        }

        return $rules;
    }

    protected static function booted(): void
    {
        static::updating(function (self $record): void {
            if ($record->getOriginal('published_at') && $record->isDirty('slug')) {
                throw ValidationException::withMessages(['slug' => 'Unpublish before changing the address.']);
            }
        });
    }
}
