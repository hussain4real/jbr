<?php

namespace App\Filament\Resources\MediaAssets;

use App\Filament\ContentEditor;
use App\Filament\Resources\MediaAssets\Pages\ManageMediaAssets;
use App\Jobs\ProcessWebsiteImage;
use App\Models\MediaAsset;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([FileUpload::make('original_path')->label('Original photograph')->disk('local')->directory('website/originals')->visibility('private')->preventFilePathTampering()->image()->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])->maxSize(12288)->required()->helperText('Up to 12 MB. Originals stay private; a queue worker creates web versions. Replacing a photograph keeps the old published image until you publish again.'),
            ...ContentEditor::translated('caption', 'Caption', 2),
            ...ContentEditor::translated('alt', 'Accessible image description', 2),
            TextInput::make('draft.focal_x')->label('Horizontal focal point (%)')->integer()->minValue(0)->maxValue(100)->default(50)->required(),
            TextInput::make('draft.focal_y')->label('Vertical focal point (%)')->integer()->minValue(0)->maxValue(100)->default(50)->required(),
            Toggle::make('draft.rights_confirmed')->label('Publication rights confirmed')->default(false),
            Toggle::make('draft.property_verified')->label('Accurately represents the resort')->default(false),
            ...ContentEditor::approvals(false), ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([...ContentEditor::columns('caption'), TextColumn::make('processing_status')->badge()])->defaultSort('id')->recordActions([...ContentEditor::actions('media'), Action::make('reprocess')->authorize('update')->visible(fn (MediaAsset $record): bool => $record->processing_status === 'failed')->action(fn (MediaAsset $record) => ProcessWebsiteImage::dispatch($record->id))]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageMediaAssets::route('/')];
    }
}
