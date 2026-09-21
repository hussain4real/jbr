<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\ContentEditor;
use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Models\Faq;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([...ContentEditor::translated('question', 'Question', 2),
            ...ContentEditor::translated('answer', 'Answer'),
            ...ContentEditor::approvals(), ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([...ContentEditor::columns('question')])->defaultSort('id')->recordActions([...ContentEditor::actions('faq')]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageFaqs::route('/')];
    }
}
