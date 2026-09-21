<?php

namespace App\Filament\Resources\GuestRequests\Pages;

use App\Filament\Resources\GuestRequests\GuestRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGuestRequests extends ManageRecords
{
    protected static string $resource = GuestRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
