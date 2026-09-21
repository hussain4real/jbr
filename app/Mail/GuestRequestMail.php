<?php

namespace App\Mail;

use App\Models\GuestRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuestRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public GuestRequest $guestRequest, public string $channel) {}

    public function envelope(): Envelope
    {
        $subject = $this->channel === 'staff' ? 'New website request' : ($this->guestRequest->locale === 'ar' ? 'استلمنا طلبك' : 'We have received your request');

        return new Envelope(subject: $subject.' · Jawharat Bidiyah Resort');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.guest-request');
    }
}
