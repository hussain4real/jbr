<?php

namespace App\Filament;

use App\Actions\Website\WebsiteRedesign;
use App\Models\Accommodation;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;

class WebsiteRedesignEditor
{
    /** @return array<Section> */
    public static function profile(): array
    {
        $source = WebsiteRedesign::source();
        $pages = [];
        foreach ($source['pages'] as $key => $page) {
            if (in_array($key, array_column($source['accommodations'], 'page'), true)) {
                continue;
            }
            $fields = [Toggle::make('draft.redesign.pages.'.$key.'.enabled')->label('Show this page')];
            foreach (['title' => 'Search title', 'description' => 'Search description'] as $field => $label) {
                array_push($fields, ...self::translated('redesign.pages.'.$key.'.'.$field, $label, 2, $key === 'home' && $field === 'description'));
            }
            foreach ($page['copy'] as $field => $translations) {
                $shared = ($key === 'home' && in_array($field, WebsiteRedesign::HOME_INTRODUCTION_COPY, true))
                    || ($key === 'contact' && $field === WebsiteRedesign::CONTACT_ARRIVAL_COPY);
                array_push($fields, ...self::translated('redesign.pages.'.$key.'.copy.'.$field, Str::limit($translations['en'], 80), mb_strlen($translations['en']) > 120 ? 4 : 2, $shared));
            }
            $description = match ($key) {
                'home' => 'Edit the welcome introduction and homepage search description in Resort information and publishing. Other homepage text is edited here.',
                'contact' => 'Edit arrival directions and contact details in Resort information and publishing. Other location page text is edited here.',
                default => null,
            };
            $pages[] = Section::make(Str::headline($key))->description($description)->schema($fields)->columns(2)->collapsed();
        }
        $images = [];
        $mediaOptions = MediaAsset::query()->get()->mapWithKeys(fn (MediaAsset $asset): array => [$asset->id => data_get($asset->draft, 'caption.en', 'Image '.$asset->id)])->all();
        foreach ($source['media'] as $key => $image) {
            $images[] = Select::make('draft.redesign.media.'.$key)->label(Str::headline($key))
                ->options($mediaOptions)
                ->searchable()->helperText('The selected image must be published before publishing this website revision.');
        }

        return [Section::make('Website v2 redesign')->description('Import the supplied v2 revision using website:import-redesign. Review both languages and media, then publish. Saving preserves the current public version.')
            ->visible(fn (?ResortProfile $record): bool => isset($record?->draft['redesign']))
            ->schema([
                TextInput::make('draft.redesign.revision')->label('Source revision')->readOnly(),
                Toggle::make('draft.redesign.facts_approved')->label('V2 facilities, services, response statements and programme content confirmed'),
                Toggle::make('draft.redesign.arabic_reviewed')->label('V2 Arabic reviewed by a competent speaker'),
                Section::make('Page content')->schema($pages)->collapsed()->columnSpanFull(),
                Section::make('Page imagery')->schema($images)->columns(2)->collapsed()->columnSpanFull(),
                Select::make('draft.redesign.gallery_media_ids')->label('Gallery photographs')->multiple()->options($mediaOptions)->searchable()->helperText('Select photos for the v2 gallery. Photos must be published and marked for gallery display.')->afterStateHydrated(function (Select $component, ?array $state, ?ResortProfile $record) use ($source): void {
                    if ($state === null) {
                        $component->state(array_values(array_unique(array_filter(array_map(fn (string $key): mixed => $record?->draft['redesign']['media'][$key] ?? null, $source['gallery'])))));
                    }
                })->columnSpanFull(),
            ])->columns(2)->collapsed()->columnSpanFull()];
    }

    /** @return array<Section> */
    public static function accommodation(): array
    {
        $fields = [TextInput::make('draft.redesign.page')->label('V2 page identifier')->readOnly()];
        foreach (['title' => 'V2 name', 'description' => 'Short description', 'overview' => 'Overview', 'inclusions' => 'Features (one per line)', 'badge' => 'Image badge', 'facts' => 'At a glance (one label and value per line)'] as $field => $label) {
            array_push($fields, ...self::translated('redesign.'.$field, $label));
        }
        $fields[] = Select::make('draft.redesign.categories')->label('Listing filters')->multiple()->options(['pool' => 'Pool', 'family' => 'Family', 'couples' => 'Couples', 'rooms' => 'Rooms']);
        $fields[] = Select::make('draft.redesign.media_ids')->label('V2 photographs')->multiple()->options(fn (): array => MediaAsset::query()->get()->mapWithKeys(fn (MediaAsset $asset): array => [$asset->id => data_get($asset->draft, 'caption.en', 'Image '.$asset->id)])->all());

        return [Section::make('V2 presentation')->schema($fields)->columns(2)->collapsed()->columnSpanFull()
            ->visible(fn (?Accommodation $record): bool => isset($record?->draft['redesign']))];
    }

    /** @return array<Textarea|Hidden> */
    private static function translated(string $path, string $label, int $rows = 3, bool $shared = false): array
    {
        if ($shared) {
            return array_map(fn (string $locale): Hidden => Hidden::make('draft.'.$path.'.'.$locale), ['en', 'ar']);
        }

        return array_map(fn (string $locale): Textarea => Textarea::make('draft.'.$path.'.'.$locale)
            ->label($label.' · '.($locale === 'ar' ? 'العربية' : 'English'))->rows($rows)->maxLength(8000)
            ->extraInputAttributes(['dir' => $locale === 'ar' ? 'rtl' : 'ltr', 'lang' => $locale]), ['en', 'ar']);
    }
}
