<?php

namespace App\Models;

use App\Actions\Website\WebsiteCopy;
use App\Concerns\PublishesWebsiteContent;
use Closure;
use Database\Factories\ResortProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * @property int $id
 * @property array<string, mixed> $draft
 * @property array<string, mixed>|null $published
 * @property Carbon|null $published_at
 */
#[Fillable(['draft', 'key'])]
class ResortProfile extends Model
{
    /** @use HasFactory<ResortProfileFactory> */
    use HasFactory, PublishesWebsiteContent;

    /** @return array<string, mixed> */
    public function publicationRules(): array
    {
        $rules = [
            'introduction.en' => 'required|string|max:2500', 'introduction.ar' => 'required|string|max:2500',
            'arabic_reviewed' => 'accepted', 'contacts_verified' => 'boolean',
            'phone' => 'nullable|string|max:32', 'email' => 'nullable|email|max:254',
            'whatsapp_verified' => 'boolean', 'whatsapp_number' => 'nullable|regex:/^\\+[1-9][0-9]{7,14}$/',
            'map_url' => 'nullable|url:https', 'instagram_url' => 'nullable|url:https', 'facebook_url' => 'nullable|url:https',
            'hero_media_id' => ['nullable', Rule::exists('media_assets', 'id')->whereNotNull('published_at')],
            'logo_light_media_id' => ['nullable', Rule::exists('media_assets', 'id')->whereNotNull('published_at')],
            'logo_dark_media_id' => ['nullable', Rule::exists('media_assets', 'id')->whereNotNull('published_at')],
            'requests_enabled' => 'boolean', 'privacy_approved' => 'boolean', 'operations_approved' => 'boolean',
            'retention_days' => 'nullable|integer|min:1|max:3650',
            'promotion_enabled' => 'boolean',
            'promotion_title.en' => 'nullable|string|max:200', 'promotion_title.ar' => 'nullable|string|max:200',
            'promotion_description.en' => 'nullable|string|max:2500', 'promotion_description.ar' => 'nullable|string|max:2500',
            'promotion_media_id' => ['nullable', Rule::exists('media_assets', 'id')->whereNotNull('published_at')],
            'promotion_pdf_path' => ['nullable', 'string', 'max:255', function (string $attribute, mixed $value, Closure $fail): void {
                if ($this->promotionPdfPath(true) === null) {
                    $fail('Upload a valid promotion PDF of up to 10 MB.');
                }
            }],
        ];
        if ($this->draft['promotion_enabled'] ?? false) {
            foreach (['promotion_title.en', 'promotion_title.ar', 'promotion_description.en', 'promotion_description.ar', 'promotion_media_id', 'promotion_pdf_path'] as $field) {
                $rules[$field] = is_array($rules[$field]) ? ['required', ...$rules[$field]] : 'required|'.$rules[$field];
            }
        }
        $copy = is_array($this->draft['copy'] ?? null) ? $this->draft['copy'] : [];
        $rules['copy'] = 'sometimes|array:'.implode(',', array_keys(WebsiteCopy::defaults()));
        foreach (WebsiteCopy::defaults() as $key => $translations) {
            $rules['copy.'.$key] = 'sometimes|array:en,ar';
            if (array_key_exists($key, $copy)) {
                $rules['copy.'.$key.'.en'] = $rules['copy.'.$key.'.ar'] = 'required|string|max:2000';
            }
        }
        foreach (['address', 'arrival', 'policies', 'privacy', 'response_hours', 'seo_description'] as $field) {
            foreach (['en', 'ar'] as $locale) {
                $rules[$field.'.'.$locale] = 'nullable|string|max:15000';
            }
            if (filled(data_get($this->draft, $field.'.en')) || filled(data_get($this->draft, $field.'.ar'))) {
                $rules[$field.'.en'] = $rules[$field.'.ar'] = 'required|string|max:15000';
            }
        }
        if ($this->draft['contacts_verified'] ?? false) {
            $rules['phone'] = 'required|string|max:32';
            $rules['email'] = 'required|email|max:254';
        }
        if ($this->draft['whatsapp_verified'] ?? false) {
            $rules['contacts_verified'] = 'accepted';
            $rules['whatsapp_number'] = 'required|regex:/^\\+[1-9][0-9]{7,14}$/';
        }
        if ($this->draft['requests_enabled'] ?? false) {
            foreach (['contacts_verified', 'privacy_approved', 'operations_approved'] as $field) {
                $rules[$field] = 'accepted';
            }
            $rules['privacy.en'] = $rules['privacy.ar'] = 'required|string|max:15000';
            $rules['retention_days'] = 'required|integer|min:1|max:3650';
        }

        return $rules;
    }

    public function promotionPdfPath(bool $draft = false): ?string
    {
        $path = ($draft ? $this->draft : $this->published)['promotion_pdf_path'] ?? null;
        if (! is_string($path) || ! preg_match('~^website/promotions/[a-zA-Z0-9_-]+\.pdf$~D', $path)) {
            return null;
        }
        $disk = Storage::disk('local');
        if (! $disk->exists($path) || $disk->mimeType($path) !== 'application/pdf' || $disk->size($path) > 10 * 1024 * 1024) {
            return null;
        }

        return $path;
    }
}
