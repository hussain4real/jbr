<?php

use App\Filament\Resources\ResortProfiles\Pages\ManageResortProfiles;
use App\Jobs\ProcessWebsiteImage;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

test('staff can save website copy and practical information as a draft then publish both languages', function () {
    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort');
    $profile = ResortProfile::factory()->published()->create();
    $published = $profile->published;
    $draft = [...$profile->draft,
        'copy' => [
            'home_title' => ['en' => 'A new welcome', 'ar' => 'ترحيب جديد'],
            'form_phone' => ['en' => 'Your contact number', 'ar' => 'رقم التواصل الخاص بك'],
            'footer_tagline' => ['en' => 'A new footer', 'ar' => 'تذييل جديد'],
        ],
        'arrival' => ['en' => 'Test entrance instructions', 'ar' => 'تعليمات المدخل التجريبية'],
        'policies' => ['en' => 'Test guest policies', 'ar' => 'سياسات تجريبية للضيوف'],
        'map_url' => 'https://maps.google.com/?q=Test',
    ];

    Livewire::test(ManageResortProfiles::class)
        ->callAction(TestAction::make('edit')->table($profile), ['draft' => $draft])
        ->assertHasNoActionErrors();

    expect($profile->fresh()->draft['copy']['home_title']['en'])->toBe('A new welcome');
    expect($profile->fresh()->published)->toBe($published);
    $this->get('/en')->assertInertia(fn (Assert $page) => $page->where('copy.home_title', 'A stay to make your own.'));

    Livewire::test(ManageResortProfiles::class)
        ->callAction(TestAction::make('publish')->table($profile))
        ->assertNotified('Published in both languages');

    $this->get('/en/contact')->assertInertia(fn (Assert $page) => $page
        ->where('copy.home_title', 'A new welcome')->where('copy.form_phone', 'Your contact number')->where('copy.footer_tagline', 'A new footer'));
    $this->get('/ar/plan-your-stay')->assertInertia(fn (Assert $page) => $page
        ->where('copy.home_title', 'ترحيب جديد')->where('profile.arrival', 'تعليمات المدخل التجريبية')
        ->where('profile.policies', 'سياسات تجريبية للضيوف')->where('profile.map_url', 'https://maps.google.com/?q=Test'));
});

test('incomplete new website copy can be saved but cannot replace the published version', function () {
    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort');
    $profile = ResortProfile::factory()->published()->create();
    $published = $profile->published;
    $draft = [...$profile->draft, 'copy' => ['home_title' => ['en' => 'English draft', 'ar' => '']]];

    Livewire::test(ManageResortProfiles::class)
        ->callAction(TestAction::make('edit')->table($profile), ['draft' => $draft])->assertHasNoActionErrors();
    Livewire::test(ManageResortProfiles::class)
        ->callAction(TestAction::make('publish')->table($profile))->assertNotified('Draft needs attention');

    expect($profile->fresh()->draft['copy']['home_title']['en'])->toBe('English draft');
    expect($profile->fresh()->published)->toBe($published);
});

test('staff can save a profile while location policies and website copy are unfinished', function () {
    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort');
    $profile = ResortProfile::factory()->create();
    $draft = [...$profile->draft, 'arrival' => ['en' => '', 'ar' => ''], 'policies' => ['en' => '', 'ar' => ''], 'map_url' => null];

    Livewire::test(ManageResortProfiles::class)
        ->callAction(TestAction::make('edit')->table($profile), ['draft' => $draft])->assertHasNoActionErrors();

    expect($profile->fresh()->published)->toBeNull();
    expect($profile->fresh()->draft['map_url'])->toBeNull();
});

test('unchanged copy uses the existing language specific defaults', function (string $locale, string $title) {
    ResortProfile::factory()->published()->create();

    $this->get('/'.$locale)->assertInertia(fn (Assert $page) => $page
        ->where('copy.home_title', $title)->where('branding.light', null)->where('branding.dark', null));
})->with([
    'English' => ['en', 'A stay to make your own.'],
    'Arabic' => ['ar', 'إقامة على طريقتك.'],
]);

test('signed staff preview renders draft copy without changing public content', function () {
    $profile = ResortProfile::factory()->published()->create();
    $profile->update(['draft' => [...$profile->draft, 'copy' => ['home_title' => ['en' => 'Preview only', 'ar' => 'للمعاينة فقط']]]]);
    $url = URL::temporarySignedRoute('website.preview', now()->addMinutes(10), ['locale' => 'en', 'type' => 'profile', 'record' => $profile->id]);

    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort')->get($url)
        ->assertInertia(fn (Assert $page) => $page->where('copy.home_title', 'Preview only')->where('acceptsRequests', false));
    $this->get('/en')->assertInertia(fn (Assert $page) => $page->where('copy.home_title', 'A stay to make your own.'));
});

test('invalid or unknown website copy cannot be published', function (array $copy) {
    $profile = ResortProfile::factory()->published()->create();
    $published = $profile->published;
    $profile->update(['draft' => [...$profile->draft, 'copy' => $copy]]);

    expect(fn () => $profile->publish())->toThrow(ValidationException::class);
    expect($profile->fresh()->published)->toBe($published);
})->with([
    'untranslated heading' => [['home_title' => ['en' => 'English only']]],
    'non-text translation' => [['home_title' => ['en' => ['unexpected'], 'ar' => 'عنوان']]],
    'oversized heading' => [['home_title' => ['en' => str_repeat('x', 2001), 'ar' => 'عنوان']]],
    'unknown setting' => [['mail_password' => ['en' => 'not public content', 'ar' => 'ليس محتوى عاماً']]],
]);

test('brand artwork is published separately from gallery and hero photographs', function () {
    Queue::fake([ProcessWebsiteImage::class]);
    $logo = MediaAsset::factory()->ready()->published()->create();
    $photo = MediaAsset::factory()->ready()->published()->create();
    $profile = ResortProfile::factory()->create();
    $profile->update(['draft' => [...$profile->draft, 'logo_light_media_id' => $logo->id]]);
    $profile->publish();

    $this->get('/en')->assertInertia(fn (Assert $page) => $page
        ->where('branding.light.id', $logo->id)->where('hero.id', $photo->id)->has('media', 1)->where('media.0.id', $photo->id));
    Queue::assertPushed(ProcessWebsiteImage::class, 2);
});

test('unpublished brand artwork cannot be published through the profile', function () {
    Queue::fake([ProcessWebsiteImage::class]);
    $logo = MediaAsset::factory()->ready()->create();
    $profile = ResortProfile::factory()->create();
    $profile->update(['draft' => [...$profile->draft, 'logo_dark_media_id' => $logo->id]]);

    expect(fn () => $profile->publish())->toThrow(ValidationException::class);
    expect($profile->fresh()->published)->toBeNull();
    Queue::assertPushed(ProcessWebsiteImage::class);
});
