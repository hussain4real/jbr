<?php

namespace App\Jobs;

use App\Mail\GuestRequestMail;
use App\Models\GuestRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

class DeliverGuestRequestNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public int $timeout = 60;

    public function __construct(public int $requestId, public string $channel)
    {
        if (! in_array($channel, ['staff', 'guest'], true)) {
            throw new RuntimeException('Unsupported notification channel.');
        }
    }

    /** @return array<int> */
    public function backoff(): array
    {
        return [30, 120, 600, 1800];
    }

    /** @return array<WithoutOverlapping> */
    public function middleware(): array
    {
        return [(new WithoutOverlapping('guest-request:'.$this->requestId.':'.$this->channel))->releaseAfter(60)->expireAfter(120)];
    }

    public function handle(): void
    {
        $record = GuestRequest::query()->find($this->requestId);
        if (! $record || $record->anonymized_at) {
            return;
        }
        $field = $this->channel.'_notification_status';
        if ($record->getAttribute($field) === 'sent') {
            return;
        }
        if (! config('website.mail_enabled')) {
            $record->forceFill([$field => 'disabled'])->save();

            return;
        }
        $recipient = $this->channel === 'staff' ? config('website.notification_email') : $record->email;
        try {
            if (! is_string($recipient) || ! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Notification recipient is not configured.');
            }
            Mail::to($recipient)->send(new GuestRequestMail($record, $this->channel));
            $record->forceFill([$field => 'sent'])->save();
        } catch (Throwable) {
            $record->forceFill([$field => 'failed'])->save();
            throw new RuntimeException('Website notification delivery failed; see the staff inbox.');
        }
    }
}
