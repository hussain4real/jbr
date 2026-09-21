<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\GuestRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property string $reference
 * @property string $kind
 * @property string $locale
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string|null $message
 * @property string|null $accommodation_label
 * @property CarbonImmutable|null $arrival
 * @property CarbonImmutable|null $departure
 * @property int|null $guests
 * @property string $status
 * @property string|null $outcome
 * @property int|null $assigned_to
 * @property string|null $reservation_reference
 * @property CarbonImmutable|null $closed_at
 * @property CarbonImmutable|null $first_contacted_at
 * @property CarbonImmutable|null $anonymized_at
 * @property string $staff_notification_status
 * @property string $guest_notification_status
 */
#[Fillable(['reference', 'submission_hash', 'kind', 'locale', 'name', 'email', 'phone', 'accommodation_id', 'accommodation_label', 'arrival', 'departure', 'guests', 'message', 'status', 'outcome', 'assigned_to', 'staff_notes', 'follow_up_at', 'reservation_reference'])]
class GuestRequest extends Model
{
    /** @use HasFactory<GuestRequestFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['arrival' => 'immutable_date', 'departure' => 'immutable_date', 'guests' => 'integer', 'follow_up_at' => 'immutable_datetime', 'closed_at' => 'immutable_datetime', 'first_contacted_at' => 'immutable_datetime', 'anonymized_at' => 'immutable_datetime'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $record): void {
            if ($record->assigned_to && ! User::query()->whereKey($record->assigned_to)->where('is_staff', true)->exists()) {
                throw ValidationException::withMessages(['assigned_to' => 'Choose an authorized staff member.']);
            }
            if ($record->outcome === 'booked' && ($record->kind !== 'booking' || blank($record->reservation_reference))) {
                throw ValidationException::withMessages(['reservation_reference' => 'A booking outcome requires the staff-issued reservation reference.']);
            }
            if ($record->status === 'closed' && blank($record->outcome)) {
                throw ValidationException::withMessages(['outcome' => 'Record the outcome before closing this request.']);
            }
            if ($record->status === 'closed') {
                $record->closed_at ??= now()->toImmutable();
            } else {
                $record->closed_at = null;
            }
            if ($record->isDirty('status') && in_array($record->status, ['contacted', 'awaiting_guest', 'closed'], true)) {
                $record->first_contacted_at ??= now()->toImmutable();
            }
        });
    }
}
