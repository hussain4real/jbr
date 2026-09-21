<?php

use App\Actions\Website\StoreGuestRequest;
use App\Jobs\DeliverGuestRequestNotification;
use App\Mail\GuestRequestMail;
use App\Models\Accommodation;
use App\Models\GuestRequest;
use App\Models\ResortProfile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

beforeEach(function () {
    Queue::fake();
    config(['website.accept_requests' => true, 'website.mail_enabled' => true, 'website.notification_email' => 'staff@example.invalid']);
    ResortProfile::factory()->published()->create();
});

function guestPayload(string $token, bool $booking = true): array
{
    return ['submission_token' => $token, 'name' => 'Test Visitor', 'email' => 'visitor@example.invalid', 'phone' => '+44 7700 900123', 'privacy' => true, 'company' => '', 'message' => 'Please contact me.', ...($booking ? ['arrival' => now('Asia/Muscat')->addDays(2)->toDateString(), 'departure' => now('Asia/Muscat')->addDays(4)->toDateString(), 'guests' => 2] : [])];
}

function guestFormToken(TestCase $test, string $path): string
{
    $response = $test->get($path);
    $test->withCookie(config('session.cookie'), session()->getId());

    return $response->inertiaProps('submissionToken');
}

test('booking requests persist before notifications and retries create one request', function () {
    $token = guestFormToken($this, '/en/request-a-booking');
    $payload = [...guestPayload($token), 'status' => 'closed', 'outcome' => 'booked', 'assigned_to' => 999];
    $this->post('/en/booking-requests', $payload)->assertSessionHasNoErrors()->assertRedirect('/en/request-received');
    $request = GuestRequest::query()->sole();
    expect($request->status)->toBe('new')->and($request->outcome)->toBeNull()->and($request->assigned_to)->toBeNull()->and($request->phone)->toBe('+447700900123');
    Queue::assertPushed(DeliverGuestRequestNotification::class, 2);
    $this->post('/en/booking-requests', $payload)->assertSessionHasNoErrors();
    expect(GuestRequest::query()->count())->toBe(1);
    Queue::assertPushed(DeliverGuestRequestNotification::class, 2);
});

test('general enquiries need no stay dates and normalize Arabic phone digits', function () {
    $token = guestFormToken($this, '/ar/contact');
    $this->post('/ar/enquiries', [...guestPayload($token, false), 'phone' => '+٩٦٨ ٩٠٠٠٠٠٠٠'])->assertSessionHasNoErrors()->assertRedirect('/ar/request-received');
    $record = GuestRequest::query()->sole();
    expect($record->kind)->toBe('enquiry')->and($record->locale)->toBe('ar')->and($record->phone)->toBe('+96890000000')->and($record->arrival)->toBeNull();
});

test('invalid or incomplete requests are not stored', function (array $invalid, string $field) {
    $token = guestFormToken($this, '/en/request-a-booking');
    $this->post('/en/booking-requests', [...guestPayload($token), ...$invalid])->assertSessionHasErrors($field);
    expect(GuestRequest::query()->count())->toBe(0);
    Queue::assertNothingPushed();
})->with([
    'email required' => [['email' => ''], 'email'],
    'phone wrong type' => [['phone' => ['bad']], 'phone'],
    'phone required' => [['phone' => ''], 'phone'],
    'phone international' => [['phone' => '90000000'], 'phone'],
    'email invalid' => [['email' => 'invalid'], 'email'],
    'past arrival' => [['arrival' => '2001-01-01'], 'arrival'],
    'reversed dates' => [['departure' => '2001-01-01'], 'departure'],
    'guest count' => [['guests' => 0], 'guests'],
    'honeypot' => [['company' => 'Spam'], 'company'],
    'privacy' => [['privacy' => false], 'privacy'],
    'token' => [['submission_token' => 'forged'], 'form'],
]);

test('draft accommodations cannot be requested', function () {
    $stay = Accommodation::factory()->create();
    $token = guestFormToken($this, '/en/request-a-booking');
    $this->post('/en/booking-requests', [...guestPayload($token), 'accommodation_id' => $stay->id])->assertSessionHasErrors('accommodation_id');
});

test('tokens expire and cannot be reused across form kinds or sessions', function () {
    $token = guestFormToken($this, '/en/request-a-booking');
    $this->post('/en/enquiries', guestPayload($token, false))->assertSessionHasErrors('form');
    $data = json_decode(Crypt::decryptString($token), true);
    $data['session'] = 'another-session';
    $this->post('/en/booking-requests', guestPayload(Crypt::encryptString(json_encode($data))))->assertSessionHasErrors('form');
    $this->travel(3)->hours();
    $this->post('/en/booking-requests', guestPayload($token))->assertSessionHasErrors('form');
});

test('launch gate applies to direct form submissions', function () {
    config(['website.accept_requests' => false]);
    $token = guestFormToken($this, '/en/request-a-booking');
    $this->post('/en/booking-requests', guestPayload($token))->assertSessionHasErrors('form');
    expect(GuestRequest::query()->count())->toBe(0);
});

test('submission throttle blocks further requests with an accessible error', function () {
    $token = guestFormToken($this, '/en/contact');
    for ($i = 0; $i < 5; $i++) {
        $this->post('/en/enquiries', guestPayload($token, false))->assertSessionHasNoErrors();
    }
    $this->post('/en/enquiries', guestPayload($token, false))->assertSessionHasErrors('form')->assertHeader('Retry-After');
});

test('mail failure retains the request and successful retries are idempotent', function () {
    $record = GuestRequest::factory()->create();
    $mailManager = Mail::getFacadeRoot();
    Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('Transport failed'));
    $job = new DeliverGuestRequestNotification($record->id, 'staff');
    expect(fn () => $job->handle())->toThrow(RuntimeException::class);
    expect($record->fresh()->staff_notification_status)->toBe('failed');
    Mail::swap($mailManager);
    Mail::fake();
    $job->handle();
    $job->handle();
    expect($record->fresh()->staff_notification_status)->toBe('sent');
    Mail::assertSent(GuestRequestMail::class, 1);
});

test('guest receipts are localized and clearly state that a booking is not confirmed', function () {
    $record = GuestRequest::factory()->create(['locale' => 'ar', 'kind' => 'booking']);
    $mail = new GuestRequestMail($record, 'guest');
    $mail->assertSeeInHtml($record->reference);
    $mail->assertSeeInHtml('الحجز');
});

test('retention anonymizes only closed requests beyond the approved period', function () {
    $old = GuestRequest::factory()->create(['status' => 'closed', 'outcome' => 'answered', 'closed_at' => now()->subDays(31)]);
    $open = GuestRequest::factory()->create(['created_at' => now()->subDays(50)]);
    $recent = GuestRequest::factory()->create(['status' => 'closed', 'outcome' => 'answered']);
    $this->artisan('website:maintain-requests')->assertSuccessful();
    expect($old->fresh()->email)->toBe('removed@example.invalid')->and($old->fresh()->anonymized_at)->not->toBeNull()->and($open->fresh()->anonymized_at)->toBeNull()->and($recent->fresh()->anonymized_at)->toBeNull();
});

test('storage failure never produces a success receipt', function () {
    $token = guestFormToken($this, '/en/request-a-booking');
    $this->mock(StoreGuestRequest::class)->shouldReceive('handle')->once()->andThrow(new RuntimeException('Storage unavailable'));
    $this->post('/en/booking-requests', guestPayload($token))->assertSessionHasErrors('form')->assertSessionMissing('website_receipt');
    expect(GuestRequest::query()->count())->toBe(0);
});
