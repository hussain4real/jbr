<?php

namespace App\Filament;

use App\Actions\Website\WebsiteCopy;
use App\Models\Accommodation;
use App\Models\Faq;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ContentEditor
{
    /** @return array<Section> */
    public static function websiteCopy(): array
    {
        $groups = [];
        foreach (WebsiteCopy::defaults() as $key => $translations) {
            [$group, $name] = explode('_', $key, 2);
            foreach ($translations as $locale => $default) {
                $groups[$group][] = Textarea::make('draft.copy.'.$key.'.'.$locale)
                    ->label(Str::headline($name).' · '.($locale === 'ar' ? 'العربية' : 'English'))
                    ->rows(mb_strlen($default) > 90 ? 3 : 2)->maxLength(2000)
                    ->extraInputAttributes(['dir' => $locale === 'ar' ? 'rtl' : 'ltr', 'lang' => $locale])
                    ->afterStateHydrated(function (Textarea $component, ?string $state) use ($default): void {
                        $component->state($state ?? $default);
                    });
            }
        }

        $sections = [];
        foreach ($groups as $group => $fields) {
            $sections[] = Section::make(Str::headline($group).' text')->schema($fields)->columns(2)->columnSpanFull()->collapsed();
        }

        return $sections;
    }

    /** @return array<Textarea> */
    public static function translated(string $field, string $label, int $rows = 3): array
    {
        return [
            Textarea::make('draft.'.$field.'.en')->label($label.' · English')->rows($rows)->maxLength(15000),
            Textarea::make('draft.'.$field.'.ar')->label($label.' · العربية')->rows($rows)->extraInputAttributes(['dir' => 'rtl', 'lang' => 'ar'])->maxLength(15000),
        ];
    }

    /** @return array<TextInput|Toggle> */
    public static function approvals(bool $facts = true): array
    {
        return [
            TextInput::make('draft.sort_order')->label('Display order')->integer()->minValue(0)->maxValue(10000)->default(0)->required(),
            Toggle::make('draft.arabic_reviewed')->label('Arabic reviewed by a competent speaker')->default(false),
            ...($facts ? [Toggle::make('draft.facts_approved')->label('Descriptions and details approved by the resort')->default(false)] : []),
        ];
    }

    /** @return array<TextColumn> */
    public static function columns(string $title): array
    {
        return [
            TextColumn::make('draft.'.$title.'.en')->label('Draft title')->wrap()->searchable(query: fn (Builder $query, string $search): Builder => $query->where('draft->'.$title.'->en', 'like', '%'.$search.'%')),
            TextColumn::make('published_at')->label('Last published')->dateTime()->placeholder('Draft only')->sortable(),
            TextColumn::make('updated_at')->label('Last edited')->since()->sortable(),
        ];
    }

    /** @return array<Action> */
    public static function actions(string $type): array
    {
        return [
            EditAction::make()->label('Edit draft')->modalDescription('Saving keeps the published version unchanged. Save, preview, then publish when both languages are approved.'),
            Action::make('preview')->authorize('update')->url(fn (Accommodation|Faq|MediaAsset|ResortProfile $record): string => URL::temporarySignedRoute('website.preview', now()->addMinutes(30), ['locale' => 'en', 'type' => $type, 'record' => $record->id]))->openUrlInNewTab(),
            Action::make('publish')->authorize('update')->color('success')->requiresConfirmation()
                ->modalDescription('Publish the saved draft in English and Arabic. Required content and approvals are checked before anything changes.')
                ->action(function (Accommodation|Faq|MediaAsset|ResortProfile $record): void {
                    Gate::authorize('update', $record);
                    try {
                        $record->publish();
                        Notification::make()->title('Published in both languages')->success()->send();
                    } catch (ValidationException $exception) {
                        Notification::make()->title('Draft needs attention')->body(implode(' ', $exception->validator->errors()->all()))->danger()->persistent()->send();
                    }
                }),
            Action::make('unpublish')->authorize('update')->color('warning')->requiresConfirmation()->visible(fn (Accommodation|Faq|MediaAsset|ResortProfile $record): bool => $record->published_at !== null)
                ->action(function (Accommodation|Faq|MediaAsset|ResortProfile $record): void {
                    Gate::authorize('update', $record);
                    $record->unpublish();
                    Notification::make()->title('Unpublished; draft preserved')->success()->send();
                }),
        ];
    }
}
