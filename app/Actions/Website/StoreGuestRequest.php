<?php

namespace App\Actions\Website;

use App\Http\Requests\GuestRequestForm;
use App\Jobs\DeliverGuestRequestNotification;
use App\Models\Accommodation;
use App\Models\GuestRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class StoreGuestRequest
{
    public function handle(GuestRequestForm $form): GuestRequest
    {
        $data = $form->validated();
        $token = json_decode(Crypt::decryptString($data['submission_token']), true, flags: JSON_THROW_ON_ERROR);
        $accommodation = isset($data['accommodation_id']) ? Accommodation::query()->whereNotNull('published_at')->whereKey($data['accommodation_id'])->first() : null;
        $record = GuestRequest::query()->firstOrCreate(
            ['submission_hash' => hash('sha256', $token['id'].':'.$token['session'])],
            [...Arr::only($data, ['name', 'email', 'phone', 'arrival', 'departure', 'guests', 'message', 'accommodation_id']),
                'reference' => (string) Str::uuid(), 'kind' => $form->kind(), 'locale' => app()->getLocale(),
                'accommodation_label' => $accommodation ? data_get($accommodation->published, 'title.'.app()->getLocale()) : null,
            ],
        );
        if ($record->wasRecentlyCreated) {
            foreach (['staff', 'guest'] as $channel) {
                try {
                    DeliverGuestRequestNotification::dispatch($record->id, $channel)->afterCommit();
                } catch (Throwable) {
                    $record->forceFill([$channel.'_notification_status' => 'failed'])->save();
                    Log::warning('Website notification could not be queued.', ['request_id' => $record->id, 'channel' => $channel]);
                }
            }
        }

        return $record;
    }
}
