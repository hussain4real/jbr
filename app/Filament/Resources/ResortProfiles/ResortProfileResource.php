<?php

namespace App\Filament\Resources\ResortProfiles;

use App\Filament\ContentEditor;
use App\Filament\Resources\ResortProfiles\Pages\ManageResortProfiles;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResortProfileResource extends Resource
{
    protected static ?string $model = ResortProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $navigationLabel = 'Website content';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Section::make('Resort information and publishing')->description('Save incomplete content as a draft. Address, arrival directions and guest policies can be added later; only published content appears on the public website.')->schema([TextInput::make('key')->default('main')->disabled()->dehydrated()->required(),
            ...ContentEditor::translated('introduction', 'Introduction'),
            Select::make('draft.hero_media_id')->label('Homepage photograph')->options(fn (): array => MediaAsset::query()->get()->mapWithKeys(fn (MediaAsset $media): array => [$media->id => data_get($media->draft, 'caption.en', 'Image '.$media->id)])->all()),
            ...ContentEditor::translated('address', 'Verified address'),
            ...ContentEditor::translated('arrival', 'Arrival directions'),
            ...ContentEditor::translated('policies', 'Guest and reservation policies', 6),
            ...ContentEditor::translated('privacy', 'Approved privacy notice', 8),
            ...ContentEditor::translated('response_hours', 'Monitored hours / response promise', 2),
            ...ContentEditor::translated('seo_description', 'Search description', 2),
            TextInput::make('draft.phone')->label('Telephone')->maxLength(32),
            TextInput::make('draft.email')->label('Guest contact email')->email()->maxLength(254),
            Toggle::make('draft.contacts_verified')->label('Telephone and email tested; monitored by staff')->default(false),
            TextInput::make('draft.whatsapp_number')->label('WhatsApp number (international format)')->maxLength(32),
            Toggle::make('draft.whatsapp_verified')->label('WhatsApp verified and monitored')->default(false),
            TextInput::make('draft.map_url')->label('Verified directions URL')->rules(['nullable', 'url:https'])->maxLength(2000),
            TextInput::make('draft.instagram_url')->label('Instagram URL')->rules(['nullable', 'url:https'])->maxLength(2000),
            TextInput::make('draft.facebook_url')->label('Facebook URL')->rules(['nullable', 'url:https'])->maxLength(2000),
            TextInput::make('draft.retention_days')->label('Approved retention after closure (days)')->integer()->minValue(1)->maxValue(3650),
            Toggle::make('draft.privacy_approved')->label('Privacy notice and retention approved by owner')->default(false),
            Toggle::make('draft.operations_approved')->label('Staff owner, availability record and confirmation process established')->default(false),
            Toggle::make('draft.requests_enabled')->label('Ready to receive requests')->default(false)->helperText('Also requires verified mail delivery and server configuration. A request never guarantees availability.'),
            Toggle::make('draft.arabic_reviewed')->label('Arabic reviewed by a competent speaker')->default(false), ])->columns(2)->columnSpanFull(),
            Section::make('Brand artwork')->description('Upload and publish artwork in Media Assets, then select it here. Leave blank to keep the supplied resort logos. Selected logos are excluded from the gallery.')->schema([
                Select::make('draft.logo_light_media_id')->label('Logo for light backgrounds')->options(fn (): array => MediaAsset::query()->get()->mapWithKeys(fn (MediaAsset $media): array => [$media->id => data_get($media->draft, 'caption.en', 'Image '.$media->id)])->all()),
                Select::make('draft.logo_dark_media_id')->label('Logo for dark backgrounds')->options(fn (): array => MediaAsset::query()->get()->mapWithKeys(fn (MediaAsset $media): array => [$media->id => data_get($media->draft, 'caption.en', 'Image '.$media->id)])->all()),
            ])->columns(2)->columnSpanFull()->collapsed(),
            Section::make('Website text')->description('Edit headings, descriptions, navigation, buttons, form labels and footer text in both languages. Existing copy is supplied as a starting point. Technical error messages and booking-status notices remain system controlled. Save, preview and publish after reviewing both languages.')->schema(ContentEditor::websiteCopy())->columnSpanFull()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([...ContentEditor::columns('introduction')])->defaultSort('id')->recordActions([...ContentEditor::actions('profile')]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageResortProfiles::route('/')];
    }
}
