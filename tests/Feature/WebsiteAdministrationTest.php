<?php

use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Filament\Resources\GuestRequests\Pages\ManageGuestRequests;
use App\Filament\Resources\MediaAssets\Pages\ManageMediaAssets;
use App\Models\Faq;
use App\Models\GuestRequest;
use App\Models\MediaAsset;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

test('administration is limited to deliberately provisioned staff', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
    $user = User::factory()->create();
    $this->actingAs($user, 'resort')->get('/admin')->assertForbidden();
    expect(Gate::forUser($user)->allows('viewAny', GuestRequest::class))->toBeFalse();
});

test('starter login cannot bypass the resort authenticator', function () {
    $staff = User::factory()->create(['is_staff' => true, 'app_authentication_secret' => 'TESTSECRET']);
    $this->actingAs($staff, 'web')->get('/admin')->assertRedirect('/admin/login');
});

test('staff must enrol in native panel MFA', function () {
    $staff = User::factory()->create(['is_staff' => true]);
    $this->actingAs($staff, 'resort')->get('/admin')->assertRedirect(route('filament.admin.auth.multi-factor-authentication.set-up-required'));
    expect(Filament::getPanel('admin')->isMultiFactorAuthenticationRequired())->toBeTrue();
});

test('public registration is disabled', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', ['email' => 'visitor@example.invalid'])->assertNotFound();
});

test('staff can preserve a draft then explicitly publish it', function () {
    $staff = User::factory()->create(['is_staff' => true]);
    $this->actingAs($staff, 'resort');
    $faq = Faq::factory()->published()->create();
    $draft = [...$faq->draft, 'answer' => ['en' => 'Updated test answer', 'ar' => 'إجابة تجريبية معدلة']];
    Livewire::test(ManageFaqs::class)->searchTable('Test question')->assertCanSeeTableRecords([$faq])
        ->callAction(TestAction::make('edit')->table($faq), ['draft' => $draft])->assertHasNoActionErrors();
    expect($faq->fresh()->published['answer']['en'])->not->toBe('Updated test answer');
    Livewire::test(ManageFaqs::class)->callAction(TestAction::make('publish')->table($faq))->assertHasNoActionErrors();
    expect($faq->fresh()->published['answer']['en'])->toBe('Updated test answer');
});

test('staff can assign and close an enquiry with a recorded outcome', function () {
    $staff = User::factory()->create(['is_staff' => true]);
    $this->actingAs($staff, 'resort');
    $record = GuestRequest::factory()->create();
    Livewire::test(ManageGuestRequests::class)->assertCanSeeTableRecords([$record])->callAction(TestAction::make('edit')->table($record), ['status' => 'closed', 'outcome' => 'answered', 'assigned_to' => $staff->id, 'staff_notes' => 'Answered by staff.'])->assertHasNoActionErrors();
    expect($record->fresh()->outcome)->toBe('answered')->and($record->fresh()->closed_at)->not->toBeNull()->and($record->fresh()->assigned_to)->toBe($staff->id);
});

test('booking outcomes require a staff confirmation reference', function () {
    $record = GuestRequest::factory()->create(['kind' => 'booking']);
    expect(fn () => $record->update(['status' => 'closed', 'outcome' => 'booked']))->toThrow(ValidationException::class);
});

test('native staff login challenges an enrolled authenticator before authentication', function () {
    $staff = User::factory()->create(['is_staff' => true, 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
    Livewire::test(Login::class)->fillForm(['email' => $staff->email, 'password' => 'password'])
        ->call('authenticate')->assertHasNoFormErrors()->assertSet('userUndertakingMultiFactorAuthentication', fn ($value): bool => filled($value));
    $this->assertGuest('resort');
});

test('media editor rejects substituting another private file path', function () {
    Queue::fake();
    Storage::fake('local');
    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort');
    $asset = MediaAsset::factory()->create();
    Storage::disk('local')->put($asset->original_path, 'test original');
    Storage::disk('local')->put('private-other.png', 'other private file');
    Livewire::test(ManageMediaAssets::class)
        ->callAction(TestAction::make('edit')->table($asset), ['original_path' => ['private-other.png'], 'draft' => $asset->draft])->assertHasActionErrors(['original_path']);
    expect($asset->fresh()->original_path)->not->toBe('private-other.png');
});
