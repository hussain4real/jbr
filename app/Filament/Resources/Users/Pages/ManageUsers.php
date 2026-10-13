<?php

namespace App\Filament\Resources\Users\Pages;

use App\Actions\ManageResortUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Validation\ValidationException;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Create resort account')->createAnother(false)
            ->modalDescription('Create a separate account with the selected role. No email is sent automatically. Then use “Send password setup link” so the account holder can set their password and enrol in mandatory authenticator verification.')
            ->using(function (array $data, ManageResortUsers $users): User {
                try {
                    return $users->create(Filament::auth()->user(), $data);
                } catch (ValidationException $exception) {
                    $statePath = $this->getMountedActionSchema()?->getStatePath();
                    $messages = [];
                    foreach ($exception->errors() as $field => $errors) {
                        $messages[$statePath ? $statePath.'.'.$field : $field] = $errors;
                    }

                    throw ValidationException::withMessages($messages);
                }
            })];
    }
}
