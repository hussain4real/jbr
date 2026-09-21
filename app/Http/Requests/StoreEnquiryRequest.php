<?php

namespace App\Http\Requests;

class StoreEnquiryRequest extends GuestRequestForm
{
    public function kind(): string
    {
        return 'enquiry';
    }
}
