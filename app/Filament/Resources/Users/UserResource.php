<?php

namespace App\Filament\Resources\Users;

use App\Actions\ManageResortUsers;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('email')->email()->required()->maxLength(254)->unique(ignoreRecord: true)->disabledOn('edit')
                ->helperText('Existing login email and credentials are managed by the account holder.'),
            Select::make('resort_role')->label('Access role')->options(User::resortRoles())->default(User::ROLE_STAFF)->required(),
            Toggle::make('is_staff')->label('Resort access enabled')->default(true)->hiddenOn('create')
                ->helperText('Disabling blocks resort access while preserving this account and its authenticator.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('resort_role')->label('Access role')->state(fn (User $record): string => User::resortRoles()[$record->resortRole() ?? ''] ?? 'No resort role')->wrap(),
            IconColumn::make('is_staff')->label('Enabled')->boolean(),
            IconColumn::make('mfa_enrolled')->label('Authenticator enrolled')->state(fn (User $record): bool => filled($record->app_authentication_secret))->boolean(),
        ])->defaultSort('id')->recordActions([
            EditAction::make()->label('Manage access')->modalDescription('Change this account’s role or disable resort access. Passwords and authenticator settings are preserved.')
                ->mutateRecordDataUsing(function (array $data, User $record): array {
                    $data['resort_role'] = $record->resortRole() ?? User::ROLE_STAFF;

                    return $data;
                })
                ->using(fn (User $record, array $data, ManageResortUsers $users): User => $users->update(Filament::auth()->user(), $record, $data)),
            Action::make('sendSetupLink')->label('Send password setup link')->authorize('sendSetupLink')->requiresConfirmation()
                ->modalDescription(fn (User $record): string => 'Email a password setup link to '.$record->email.'. This does not change the password or disable authenticator verification.')
                ->action(function (User $record, ManageResortUsers $users): void {
                    $users->sendSetupLink(Filament::auth()->user(), $record);
                    Notification::make()->title('Password setup email requested')->success()->send();
                }),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageUsers::route('/')];
    }
}
