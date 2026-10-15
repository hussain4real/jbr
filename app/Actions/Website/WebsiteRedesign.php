<?php

namespace App\Actions\Website;

use Illuminate\Validation\Rule;
use RuntimeException;

class WebsiteRedesign
{
    public const HOME_INTRODUCTION_COPY = ['2_section_0_div_0_div_2_p_0', '2_section_0_div_0_div_3_p_0'];

    public const CONTACT_ARRIVAL_COPY = '2_section_0_div_0_div_0_p_0';

    /** @return array<string, mixed> */
    public static function source(): array
    {
        static $source;

        if ($source === null) {
            $contents = file_get_contents(resource_path('website-v2.json'));
            if ($contents === false) {
                throw new RuntimeException('The supplied website content manifest could not be read.');
            }
            $source = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
            if (! is_array($source)) {
                throw new RuntimeException('The supplied website content manifest is invalid.');
            }
        }

        return $source;
    }

    /**
     * @param  array<string, mixed>  $draft
     * @return array<string, mixed>
     */
    public static function publicationRules(array $draft): array
    {
        if (! isset($draft['redesign'])) {
            return [];
        }
        $source = self::source();
        $rules = [
            'redesign' => 'array:revision,facts_approved,arabic_reviewed,pages,media,gallery_media_ids',
            'redesign.revision' => ['required', Rule::in([$source['revision']])],
            'redesign.facts_approved' => 'accepted',
            'redesign.arabic_reviewed' => 'accepted',
            'redesign.pages' => 'required|array:'.implode(',', array_keys($source['pages'])),
            'redesign.media' => 'present|array:'.implode(',', array_keys($source['media'])),
            'redesign.media.*' => ['nullable', 'integer', Rule::exists('media_assets', 'id')->whereNotNull('published_at')],
            'redesign.gallery_media_ids' => 'sometimes|array|max:100',
            'redesign.gallery_media_ids.*' => ['integer', Rule::exists('media_assets', 'id')->whereNotNull('published_at')],
        ];
        foreach ($source['pages'] as $key => $page) {
            $path = 'redesign.pages.'.$key;
            $rules[$path] = 'sometimes|array:enabled,title,description,copy';
            $rules[$path.'.enabled'] = 'required_with:'.$path.'|boolean';
            $required = data_get($draft, $path.'.enabled') ? 'required' : 'nullable';
            foreach (['title', 'description'] as $field) {
                $rules[$path.'.'.$field] = $required.'|array:en,ar';
                foreach (['en', 'ar'] as $locale) {
                    $rules[$path.'.'.$field.'.'.$locale] = $required.'|string|max:2000';
                }
            }
            $rules[$path.'.copy'] = $required.'|array:'.implode(',', array_keys($page['copy']));
            foreach ($page['copy'] as $field => $translations) {
                $rules[$path.'.copy.'.$field] = $required.'|array:en,ar';
                foreach (['en', 'ar'] as $locale) {
                    $rules[$path.'.copy.'.$field.'.'.$locale] = $required.'|string|max:5000';
                }
            }
        }

        return $rules;
    }

    /**
     * @param  array<string, mixed>  $profile
     * @param  array<int, array<string, mixed>>  $media
     * @return array<string, mixed>|null
     */
    public static function forLocale(array $profile, array $media, string $locale): ?array
    {
        $redesign = $profile['redesign'] ?? null;
        if (! is_array($redesign)) {
            return null;
        }
        $pages = [];
        foreach ($redesign['pages'] ?? [] as $key => $page) {
            if (! ($page['enabled'] ?? false)) {
                continue;
            }
            $pages[$key] = [
                'title' => data_get($page, 'title.'.$locale, ''),
                'description' => data_get($page, 'description.'.$locale, ''),
                'copy' => array_map(fn (array $text): string => $text[$locale] ?? '', $page['copy'] ?? []),
            ];
        }
        if (isset($pages['home'])) {
            $introduction = data_get($profile, 'introduction.'.$locale);
            if (is_string($introduction)) {
                $paragraphs = preg_split('/\R\s*\R/u', trim($introduction)) ?: [];
                $pages['home']['copy'][self::HOME_INTRODUCTION_COPY[0]] = array_shift($paragraphs) ?? '';
                $pages['home']['copy'][self::HOME_INTRODUCTION_COPY[1]] = implode("\n\n", $paragraphs);
            }
            $description = data_get($profile, 'seo_description.'.$locale);
            if (is_string($description)) {
                $pages['home']['description'] = $description;
            }
        }
        $arrival = data_get($profile, 'arrival.'.$locale);
        if (isset($pages['contact']) && is_string($arrival)) {
            $pages['contact']['copy'][self::CONTACT_ARRIVAL_COPY] = $arrival;
        }
        $images = [];
        foreach ($redesign['media'] ?? [] as $key => $id) {
            if (isset($media[$id])) {
                $images[$key] = $media[$id];
            }
        }

        $galleryIds = $redesign['gallery_media_ids'] ?? array_map(fn (string $key): mixed => $redesign['media'][$key] ?? null, self::source()['gallery']);
        $gallery = [];
        foreach (array_unique($galleryIds) as $id) {
            if ((is_int($id) || is_string($id)) && isset($media[$id]) && ($media[$id]['galleryVisible'] ?? true)) {
                $gallery[] = $media[$id];
            }
        }

        return ['revision' => $redesign['revision'] ?? '', 'pages' => $pages, 'images' => $images, 'gallery' => $gallery];
    }
}
