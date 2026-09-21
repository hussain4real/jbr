<?php

namespace App\Filament\Resources\Accommodations;

use App\Filament\ContentEditor;
use App\Filament\Resources\Accommodations\Pages\ManageAccommodations;
use App\Models\Accommodation;
use App\Models\MediaAsset;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AccommodationResource extends Resource
{
    protected static ?string $model = Accommodation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('slug')->required()->alphaDash()->maxLength(180)->unique(ignoreRecord: true)->helperText('Unpublish before changing an existing public address.'),
            ...ContentEditor::translated('title', 'Name', 1),
            ...ContentEditor::translated('description', 'Description'),
            ...ContentEditor::translated('inclusions', 'Confirmed inclusions'),
            TextInput::make('draft.capacity')->label('Maximum guests')->integer()->minValue(1)->maxValue(1000),
            Select::make('draft.media_ids')->label('Photographs')->multiple()->options(fn (): array => MediaAsset::query()->get()->mapWithKeys(fn (MediaAsset $media): array => [$media->id => data_get($media->draft, 'caption.en', 'Image '.$media->id)])->all()),
            ...ContentEditor::approvals(), ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([...ContentEditor::columns('title')])->defaultSort('id')->recordActions([...ContentEditor::actions('accommodation')]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAccommodations::route('/')];
    }
}
