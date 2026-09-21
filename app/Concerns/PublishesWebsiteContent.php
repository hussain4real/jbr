<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * @property array<string, mixed> $draft
 * @property array<string, mixed>|null $published
 *
 * @mixin Model
 */
trait PublishesWebsiteContent
{
    protected function initializePublishesWebsiteContent(): void
    {
        $this->mergeCasts(['draft' => 'array', 'published' => 'array', 'published_at' => 'immutable_datetime']);
    }

    /** @return array<string, mixed> */
    abstract public function publicationRules(): array;

    public function publish(): void
    {
        $validated = Validator::make($this->draft, $this->publicationRules())->validate();
        $this->forceFill(['published' => $validated, 'published_at' => now()])->save();
    }

    public function unpublish(): void
    {
        $this->forceFill(['published' => null, 'published_at' => null])->save();
    }
}
