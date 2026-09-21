<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreBookingRequest extends GuestRequestForm
{
    public function kind(): string
    {
        return 'booking';
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [...parent::rules(),
            'arrival' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.now(config('website.timezone'))->toDateString()],
            'departure' => ['required', 'date_format:Y-m-d', 'after:arrival'],
            'guests' => ['required', 'integer', 'min:1', 'max:1000'],
            'accommodation_id' => ['nullable', 'integer', Rule::exists('accommodations', 'id')->whereNotNull('published_at')],
        ];
    }
}
