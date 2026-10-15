<?php

namespace App\Http\Requests;

class StoreEnquiryRequest extends GuestRequestForm
{
    public function kind(): string
    {
        return 'enquiry';
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [...parent::rules(),
            'arrival' => ['nullable', 'required_with:departure', 'date_format:Y-m-d', 'after_or_equal:'.now(config('website.timezone'))->toDateString()],
            'departure' => ['nullable', 'required_with:arrival', 'date_format:Y-m-d', 'after:arrival'],
        ];
    }
}
