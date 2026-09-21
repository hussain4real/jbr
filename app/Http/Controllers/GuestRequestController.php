<?php

namespace App\Http\Controllers;

use App\Actions\Website\StoreGuestRequest;
use App\Http\Requests\GuestRequestForm;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\StoreEnquiryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class GuestRequestController extends Controller
{
    public function booking(StoreBookingRequest $request, StoreGuestRequest $store): RedirectResponse
    {
        return $this->store($request, $store);
    }

    public function enquiry(StoreEnquiryRequest $request, StoreGuestRequest $store): RedirectResponse
    {
        return $this->store($request, $store);
    }

    private function store(GuestRequestForm $request, StoreGuestRequest $store): RedirectResponse
    {
        try {
            $record = $store->handle($request);
        } catch (Throwable) {
            Log::error('A website request could not be saved.');

            return back()->withErrors(['form' => app()->getLocale() === 'ar' ? 'تعذّر حفظ طلبك. يرجى المحاولة مجدداً.' : 'We could not save your request. Please try again.']);
        }
        $request->session()->put('website_receipt', ['reference' => $record->reference, 'kind' => $record->kind]);

        return to_route('website.received', ['locale' => app()->getLocale()], 303);
    }
}
