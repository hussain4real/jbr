<?php

use App\Models\Accommodation;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia as Assert;

test('only published content is visible in either language', function (string $locale) {
    $profile = ResortProfile::factory()->published()->create();
    $profile->update(['draft' => [...$profile->draft, 'introduction' => ['en' => 'SECRET DRAFT', 'ar' => 'مسودة سرية']]]);
    Accommodation::factory()->create();
    $this->get('/'.$locale)->assertOk()->assertHeader('Content-Language', $locale)->assertInertia(fn (Assert $page) => $page
        ->component('public/Website')->where('locale', $locale)->where('profile.introduction', $profile->published['introduction'][$locale])->has('accommodations', 0)->where('isPreview', false));
    $this->get('/'.$locale.'/accommodation')->assertNotFound();
    $this->get('/'.$locale.'/accommodation/missing')->assertNotFound();
})->with(['en', 'ar']);

test('unsupported locales and empty unpublished sections return not found', function () {
    $this->get('/fr')->assertNotFound();
    $this->get('/en/gallery')->assertNotFound();
    $this->get('/en/privacy')->assertNotFound();
});

test('receipt is session bound and is not indexable', function () {
    $this->get('/en/request-received')->assertRedirect('/en');
    $this->withSession(['website_receipt' => ['reference' => 'test-reference', 'kind' => 'booking']])->get('/ar/request-received')
        ->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertHeader('Cache-Control', 'no-store, private');
});

test('changing language retains the selected accommodation', function () {
    $this->get('/en/request-a-booking?accommodation=42')->assertInertia(fn (Assert $page) => $page->where('alternateUrl', url('/ar/request-a-booking?accommodation=42')));
});

test('draft preview requires both staff session and a valid signature', function () {
    $profile = ResortProfile::factory()->create();
    $url = URL::temporarySignedRoute('website.preview', now()->addMinutes(10), ['locale' => 'en', 'type' => 'profile', 'record' => $profile->id]);
    $this->get($url)->assertRedirect();
    $this->actingAs(User::factory()->create(), 'resort')->get($url)->assertForbidden();
    $staff = User::factory()->create(['is_staff' => true]);
    $this->actingAs($staff, 'resort')->get($url.'&tampered=1')->assertForbidden();
    $this->actingAs($staff, 'resort')->get($url)->assertOk()->assertInertia(fn (Assert $page) => $page->where('isPreview', true)->where('acceptsRequests', false)->where('indexable', false));
});

test('local preview flag cannot expose drafts in production', function () {
    app()->detectEnvironment(fn () => 'production');
    config(['website.local_preview' => true]);
    ResortProfile::factory()->create();
    $this->get('/en')->assertInertia(fn (Assert $page) => $page->where('isPreview', false)->where('profile.introduction', ''));
});

test('sitemap excludes private and unpublished pages', function () {
    config(['website.indexable' => true]);
    ResortProfile::factory()->published()->create();
    Accommodation::factory()->create(['slug' => 'draft-only']);
    $this->get('/sitemap.xml')->assertOk()->assertSee('/ar/privacy')->assertDontSee('draft-only')->assertDontSee('request-received')->assertDontSee('preview');
});

test('staff can preview selected draft photographs before publishing an accommodation', function () {
    Queue::fake();
    $media = MediaAsset::factory()->ready()->create();
    $stay = Accommodation::factory()->create();
    $stay->update(['draft' => [...$stay->draft, 'media_ids' => [$media->id]]]);
    $url = URL::temporarySignedRoute('website.preview', now()->addMinutes(10), ['locale' => 'en', 'type' => 'accommodation', 'record' => $stay->id]);
    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort')->get($url)->assertInertia(fn (Assert $page) => $page->has('stay.images', 1)->where('isPreview', true));
    expect($media->fresh()->published)->toBeNull()->and($stay->fresh()->published)->toBeNull();
});

test('indexing cannot expose an unapproved profile or local preview sitemap', function () {
    config(['website.indexable' => true]);
    ResortProfile::factory()->create();
    $this->get('/en')->assertHeader('X-Robots-Tag', 'noindex, nofollow')->assertInertia(fn (Assert $page) => $page->where('indexable', false));
    $this->get('/sitemap.xml')->assertDontSee('<loc>', escape: false);
    ResortProfile::query()->sole()->publish();
    app()->detectEnvironment(fn () => 'local');
    config(['website.local_preview' => true]);
    $this->get('/sitemap.xml')->assertDontSee('<loc>', escape: false);
});
