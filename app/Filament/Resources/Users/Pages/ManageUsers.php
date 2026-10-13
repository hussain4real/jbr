<?php

namespace App\Filament\Resources\Users\Pages;

use App\Actions\ManageResortUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    /** @return array{url: string, expiresAt: string} */
    public function generateSetupLink(int $targetId, int $minutes, ManageResortUsers $users): array
    {
        $actor = Filament::auth()->user();
        abort_unless($actor instanceof User, 403);
        Gate::forUser($actor)->authorize('viewAny', User::class);

        return $users->generateSetupLink($actor, User::query()->findOrFail($targetId), $minutes);
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Create resort account')->createAnother(false)
            ->modalDescription('Create a separate account with the selected role. No email is sent automatically. Then use “Generate and copy setup link” for private sharing, or “Send password setup link” so the account holder can set their password and enrol in mandatory authenticator verification.')
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
