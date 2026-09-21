<?php

namespace App\Filament\Resources\GuestRequests;

use App\Filament\Resources\GuestRequests\Pages\ManageGuestRequests;
use App\Jobs\DeliverGuestRequestNotification;
use App\Models\GuestRequest;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class GuestRequestResource extends Resource
{
    protected static ?string $model = GuestRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $navigationLabel = 'Guest inbox';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            ...array_map(fn (string $field): TextInput => TextInput::make($field)->disabled()->dehydrated(false), ['reference', 'kind', 'locale', 'name', 'email', 'phone', 'accommodation_label', 'arrival', 'departure', 'guests']),
            Textarea::make('message')->disabled()->dehydrated(false)->columnSpanFull(),
            Select::make('assigned_to')->label('Responsible staff')->options(fn (): array => User::query()->where('is_staff', true)->pluck('name', 'id')->all()),
            Select::make('status')->options(['new' => 'New', 'contacted' => 'Contacted', 'awaiting_guest' => 'Awaiting guest', 'closed' => 'Closed'])->required(),
            DateTimePicker::make('follow_up_at')->timezone('Asia/Muscat')->label('Follow up (Oman time)'),
            Select::make('outcome')->options(['booked' => 'Reservation confirmed by staff', 'unavailable' => 'Dates unavailable', 'answered' => 'Enquiry answered', 'declined' => 'Guest declined', 'no_response' => 'No response', 'spam' => 'Spam'])->helperText('A submission is not a booking. Record booked only after issuing a reservation confirmation.'),
            TextInput::make('reservation_reference')->label('Staff-issued reservation reference')->maxLength(255),
            Textarea::make('staff_notes')->rows(5)->maxLength(10000)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('reference')->limit(8)->tooltip(fn (GuestRequest $record): string => $record->reference)->searchable(),
            TextColumn::make('name')->searchable(), TextColumn::make('kind')->badge(),
            TextColumn::make('arrival')->date()->sortable(), TextColumn::make('status')->badge(),
            TextColumn::make('staff_notification_status')->label('Staff email')->badge(),
            TextColumn::make('guest_notification_status')->label('Guest receipt')->badge(),
            TextColumn::make('follow_up_at')->dateTime()->timezone('Asia/Muscat')->sortable(),
            TextColumn::make('created_at')->dateTime()->timezone('Asia/Muscat')->sortable(),
        ])->defaultSort('created_at', 'desc')->filters([
            SelectFilter::make('status')->options(['new' => 'New', 'contacted' => 'Contacted', 'awaiting_guest' => 'Awaiting guest', 'closed' => 'Closed']),
            SelectFilter::make('assigned_to')->options(fn (): array => User::query()->where('is_staff', true)->pluck('name', 'id')->all()),
            Filter::make('due')->label('Follow-up due')->query(fn (Builder $query): Builder => $query->where('status', '!=', 'closed')->where('follow_up_at', '<=', now())),
        ])->recordActions([
            EditAction::make()->label('Review / follow up')->visible(fn (GuestRequest $record): bool => $record->anonymized_at === null),
            Action::make('retry_notifications')->authorize('update')->label('Retry failed emails')->requiresConfirmation()
                ->visible(fn (GuestRequest $record): bool => $record->anonymized_at === null && ($record->staff_notification_status !== 'sent' || $record->guest_notification_status !== 'sent'))
                ->action(function (GuestRequest $record): void {
                    Gate::authorize('update', $record);
                    foreach (['staff', 'guest'] as $channel) {
                        if ($record->{$channel.'_notification_status'} !== 'sent') {
                            DeliverGuestRequestNotification::dispatch($record->id, $channel);
                        }
                    }
                    Notification::make()->title('Unsent notifications queued')->success()->send();
                }),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageGuestRequests::route('/')];
    }
}
