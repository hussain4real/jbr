<?php

namespace App\Filament\Resources\ResortProfiles\Pages;

use App\Filament\Resources\ResortProfiles\ResortProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageResortProfiles extends ManageRecords
{
    protected static string $resource = ResortProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
