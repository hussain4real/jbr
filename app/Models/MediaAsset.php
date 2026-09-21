<?php

namespace App\Models;

use App\Concerns\PublishesWebsiteContent;
use App\Jobs\ProcessWebsiteImage;
use Database\Factories\MediaAssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property array<string, mixed> $draft
 * @property array<string, mixed>|null $published
 * @property Carbon|null $published_at
 * @property string $original_path
 * @property string $processing_status
 * @property array<int, array{path: string, width: int, height: int}>|null $variants
 */
#[Fillable(['draft', 'original_path'])]
class MediaAsset extends Model
{
    /** @use HasFactory<MediaAssetFactory> */
    use HasFactory, PublishesWebsiteContent;

    /** @return array<string, mixed> */
    public function publicationRules(): array
    {
        return [
            'alt.en' => 'required|string|max:300', 'alt.ar' => 'required|string|max:300',
            'caption.en' => 'required|string|max:500', 'caption.ar' => 'required|string|max:500',
            'rights_confirmed' => 'accepted', 'property_verified' => 'accepted', 'arabic_reviewed' => 'accepted',
            'sort_order' => 'required|integer|min:0|max:10000', 'focal_x' => 'required|integer|min:0|max:100',
            'focal_y' => 'required|integer|min:0|max:100',
        ];
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['variants' => 'array'];
    }

    protected static function booted(): void
    {
        static::created(function (self $record): void {
            ProcessWebsiteImage::dispatch($record->id)->afterCommit();
        });
        static::updated(function (self $record): void {
            if ($record->wasChanged('original_path')) {
                $record->forceFill(['processing_status' => 'pending', 'variants' => null])->saveQuietly();
                ProcessWebsiteImage::dispatch($record->id)->afterCommit();
            }
        });
    }

    public function publish(): void
    {
        if ($this->processing_status !== 'ready' || ! $this->variants) {
            throw ValidationException::withMessages(['original_path' => 'Image processing must complete before publishing.']);
        }
        $validated = Validator::make($this->draft, $this->publicationRules())->validate();
        $this->forceFill(['published' => [...$validated, 'variants' => $this->variants], 'published_at' => now()])->save();
    }
}
