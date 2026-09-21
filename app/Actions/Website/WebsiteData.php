<?php

namespace App\Actions\Website;

use App\Models\Accommodation;
use App\Models\Faq;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class WebsiteData
{
    public static function localPreview(): bool
    {
        return app()->environment('local') && (bool) config('website.local_preview');
    }

    /** @return array<string, mixed> */
    public function publishedProfile(): array
    {
        $profile = ResortProfile::query()->where('key', 'main')->first();
        if (! $profile) {
            return [];
        }

        return $profile->published ?? [];
    }

    public function isIndexable(): bool
    {
        return (bool) config('website.indexable') && ! self::localPreview() && filled($this->publishedProfile());
    }

    public function acceptsRequests(): bool
    {
        $profile = $this->publishedProfile();

        return ! self::localPreview() && (bool) config('website.accept_requests') && (bool) config('website.mail_enabled')
            && filter_var(config('website.notification_email'), FILTER_VALIDATE_EMAIL)
            && ($profile['requests_enabled'] ?? false) && ($profile['contacts_verified'] ?? false)
            && ($profile['privacy_approved'] ?? false) && ($profile['operations_approved'] ?? false)
            && ($profile['retention_days'] ?? 0) > 0 && filled(data_get($profile, 'privacy.en')) && filled(data_get($profile, 'privacy.ar'));
    }

    /** @return array<string, mixed> */
    public function forLocale(string $locale, ?Model $preview = null): array
    {
        $localPreview = self::localPreview();
        $profileRecord = ResortProfile::query()->where('key', 'main')->first();
        $profile = ($preview instanceof ResortProfile ? $preview->draft : ($localPreview ? $profileRecord?->draft : $profileRecord?->published)) ?? [];
        $previewMediaIds = match (true) {
            $preview instanceof Accommodation => $preview->draft['media_ids'] ?? [],
            $preview instanceof ResortProfile => [$preview->draft['hero_media_id'] ?? null, $preview->draft['logo_light_media_id'] ?? null, $preview->draft['logo_dark_media_id'] ?? null],
            default => [],
        };
        $media = [];
        foreach (MediaAsset::query()->orderBy('id')->get() as $asset) {
            $isDraft = $localPreview || ($preview instanceof MediaAsset && $preview->is($asset)) || in_array($asset->id, $previewMediaIds);
            $content = $isDraft ? $asset->draft : $asset->published;
            $variants = $isDraft ? $asset->variants : ($content['variants'] ?? null);
            if (! $content || ! is_array($variants) || ! isset($variants['960'])) {
                continue;
            }
            $urls = [];
            foreach ($variants as $size => $variant) {
                $params = ['asset' => $asset->id, 'size' => $size, 'version' => substr(hash('sha256', $variant['path']), 0, 16)];
                $urls[$size] = $isDraft ? URL::temporarySignedRoute('website.media', now()->addHour(), [...$params, 'preview' => 1]) : route('website.media', $params);
            }
            $uniqueSizes = collect(array_keys($variants))->unique(fn ($size): int => $variants[$size]['width'])->all();
            $media[$asset->id] = [
                'id' => $asset->id, 'src' => $urls[960], 'large' => $urls[1600],
                'srcset' => implode(', ', array_map(fn ($size): string => $urls[$size].' '.$variants[$size]['width'].'w', $uniqueSizes)),
                'width' => $variants[960]['width'], 'height' => $variants[960]['height'],
                'alt' => data_get($content, 'alt.'.$locale, ''), 'caption' => data_get($content, 'caption.'.$locale, ''),
                'position' => ($content['focal_x'] ?? 50).'% '.($content['focal_y'] ?? 50).'%', 'order' => (int) ($content['sort_order'] ?? 0),
            ];
        }
        uasort($media, fn (array $a, array $b): int => [$a['order'], $a['id']] <=> [$b['order'], $b['id']]);
        $logoIds = array_filter([$profile['logo_light_media_id'] ?? null, $profile['logo_dark_media_id'] ?? null]);
        $gallery = array_diff_key($media, array_flip($logoIds));
        $accommodations = [];
        foreach (Accommodation::query()->orderBy('id')->get() as $record) {
            $content = ($localPreview || ($preview instanceof Accommodation && $preview->is($record))) ? $record->draft : $record->published;
            if (! $content) {
                continue;
            }
            $images = array_values(array_intersect_key($media, array_flip($content['media_ids'] ?? [])));
            $accommodations[] = ['id' => $record->id, 'slug' => $record->slug,
                'title' => data_get($content, 'title.'.$locale, ''), 'description' => data_get($content, 'description.'.$locale, ''),
                'inclusions' => data_get($content, 'inclusions.'.$locale, ''), 'capacity' => $content['capacity'] ?? null,
                'images' => $images, 'order' => (int) ($content['sort_order'] ?? 0), 'published' => $record->published_at !== null,
            ];
        }
        usort($accommodations, fn (array $a, array $b): int => [$a['order'], $a['id']] <=> [$b['order'], $b['id']]);
        $faqs = [];
        foreach (Faq::query()->orderBy('id')->get() as $record) {
            $content = ($localPreview || ($preview instanceof Faq && $preview->is($record))) ? $record->draft : $record->published;
            if (! $content) {
                continue;
            }
            $faqs[] = ['id' => $record->id, 'question' => data_get($content, 'question.'.$locale, ''), 'answer' => data_get($content, 'answer.'.$locale, ''), 'order' => (int) ($content['sort_order'] ?? 0)];
        }
        usort($faqs, fn (array $a, array $b): int => [$a['order'], $a['id']] <=> [$b['order'], $b['id']]);
        $translated = [];
        foreach (['introduction', 'address', 'arrival', 'policies', 'privacy', 'response_hours', 'seo_description'] as $key) {
            $translated[$key] = data_get($profile, $key.'.'.$locale, '');
        }

        return [
            'locale' => $locale, 'isPreview' => $preview !== null || $localPreview,
            'copy' => WebsiteCopy::forLocale($profile, $locale),
            'branding' => [
                'light' => $media[$profile['logo_light_media_id'] ?? 0] ?? null,
                'dark' => $media[$profile['logo_dark_media_id'] ?? 0] ?? null,
            ],
            'profile' => [...$translated,
                'phone' => ($profile['contacts_verified'] ?? false) ? ($profile['phone'] ?? null) : null,
                'email' => ($profile['contacts_verified'] ?? false) ? ($profile['email'] ?? null) : null,
                'whatsapp' => ($profile['whatsapp_verified'] ?? false) ? ($profile['whatsapp_number'] ?? null) : null,
                'map_url' => $profile['map_url'] ?? null, 'instagram_url' => $profile['instagram_url'] ?? null, 'facebook_url' => $profile['facebook_url'] ?? null,
            ],
            'hero' => $gallery[$profile['hero_media_id'] ?? 0] ?? (array_values($gallery)[0] ?? null),
            'media' => array_values($gallery), 'accommodations' => $accommodations, 'faqs' => $faqs,
            'acceptsRequests' => ! $preview && ! $localPreview && $this->acceptsRequests(),
            'indexable' => ! $preview && $this->isIndexable(),
        ];
    }
}
