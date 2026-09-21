<?php

namespace App\Console\Commands;

use App\Actions\Website\WebsiteData;
use App\Jobs\DeliverGuestRequestNotification;
use App\Models\GuestRequest;
use Illuminate\Console\Command;

class MaintainGuestRequests extends Command
{
    protected $signature = 'website:maintain-requests';

    protected $description = 'Retry undelivered notifications and anonymize closed requests using approved retention';

    public function handle(WebsiteData $data): int
    {
        $retention = (int) ($data->publishedProfile()['retention_days'] ?? 0);
        if ($retention > 0 && ($data->publishedProfile()['privacy_approved'] ?? false)) {
            GuestRequest::query()->where('status', 'closed')->where('closed_at', '<=', now()->subDays($retention))->whereNull('anonymized_at')->update([
                'name' => 'Removed', 'email' => 'removed@example.invalid', 'phone' => 'Removed', 'message' => null, 'staff_notes' => null,
                'reservation_reference' => null, 'arrival' => null, 'departure' => null, 'guests' => null, 'follow_up_at' => null, 'anonymized_at' => now(),
            ]);
        }
        if (config('website.mail_enabled')) {
            GuestRequest::query()->whereNull('anonymized_at')->where('created_at', '<', now()->subMinutes(15))->where('created_at', '>=', now()->subDays(7))->chunkById(100, function ($requests): void {
                foreach ($requests as $request) {
                    foreach (['staff', 'guest'] as $channel) {
                        if ($request->{$channel.'_notification_status'} !== 'sent') {
                            DeliverGuestRequestNotification::dispatch($request->id, $channel);
                        }
                    }
                }
            });
        }
        $this->info('Retention and notification recovery checks completed.');

        return self::SUCCESS;
    }
}
