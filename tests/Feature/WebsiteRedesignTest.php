<?php

use App\Actions\Website\WebsiteData;
use App\Actions\Website\WebsiteRedesign;
use App\Filament\Resources\ResortProfiles\Pages\ManageResortProfiles;
use App\Models\Accommodation;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;

function prepareWebsiteRedesign(): ResortProfile
{
    $source = WebsiteRedesign::source();
    $profile = ResortProfile::factory()->published()->create();
    $profile->update(['draft' => [...$profile->draft, 'redesign' => [
        'revision' => $source['revision'], 'facts_approved' => true, 'arabic_reviewed' => true,
        'pages' => ['home' => $source['pages']['home'], 'experiences' => $source['pages']['experiences']], 'media' => [],
    ]]]);

    return $profile;
}

test('v2 draft content is hidden until bilingual publication and stays unchanged while edited', function (string $locale) {
    $profile = prepareWebsiteRedesign();
    $this->get('/'.$locale)->assertInertia(fn (Assert $page) => $page->where('redesign', null));

    $profile->publish();
    $profile->update(['draft' => [...$profile->draft, 'redesign' => [...$profile->draft['redesign'], 'pages' => [...$profile->draft['redesign']['pages'], 'home' => [...$profile->draft['redesign']['pages']['home'], 'title' => ['en' => 'SECRET', 'ar' => 'سري']]]]]]);

    $this->get('/'.$locale)->assertInertia(fn (Assert $page) => $page
        ->where('redesign.pages.home.title', WebsiteRedesign::source()['pages']['home']['title'][$locale])
        ->where('redesign.pages.experiences.description', WebsiteRedesign::source()['pages']['experiences']['description'][$locale]));
})->with(['en', 'ar']);

test('incomplete or unapproved v2 drafts cannot replace the public snapshot', function (string $path, mixed $value) {
    $profile = prepareWebsiteRedesign();
    $before = $profile->published;
    $draft = $profile->draft;
    data_set($draft, $path, $value);
    $profile->update(['draft' => $draft]);

    expect(fn () => $profile->publish())->toThrow(ValidationException::class);
    expect($profile->fresh()->published)->toEqual($before);
})->with([
    'facts not approved' => ['redesign.facts_approved', false],
    'Arabic not reviewed' => ['redesign.arabic_reviewed', false],
    'missing Arabic page title' => ['redesign.pages.home.title.ar', ''],
    'missing Arabic paragraph' => ['redesign.pages.home.copy.0_section_0_div_0_img_attr_alt.ar', ''],
    'unknown source revision' => ['redesign.revision', 'future'],
]);

test('signed profile preview includes v2 draft copy and draft images without making them public', function () {
    Queue::fake();
    $profile = prepareWebsiteRedesign();
    $image = MediaAsset::factory()->ready()->create();
    $profile->update(['draft' => [...$profile->draft, 'redesign' => [...$profile->draft['redesign'], 'media' => ['hero-pool2' => $image->id]]]]);
    $url = URL::temporarySignedRoute('website.preview', now()->addMinutes(10), ['locale' => 'ar', 'type' => 'profile', 'record' => $profile->id]);

    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort')->get($url)
        ->assertInertia(fn (Assert $page) => $page->where('isPreview', true)->where('redesign.images.hero-pool2.id', $image->id)->where('acceptsRequests', false));
    expect($image->fresh()->published)->toBeNull();
    expect(app(WebsiteData::class)->forLocale('ar')['redesign'])->toBeNull();
    expect(fn () => $profile->publish())->toThrow(ValidationException::class);
});

test('v2 import preserves reviewed edits capacities publications and notification settings across reruns', function () {
    Storage::fake('local');
    Queue::fake();
    $profile = ResortProfile::factory()->published()->create();
    $profileBefore = $profile->published;
    $stay = Accommodation::factory()->create(['slug' => 'oman-villa']);
    $stay->update(['draft' => [...$stay->draft, 'capacity' => 7, 'description' => ['en' => 'Reviewed staff description', 'ar' => 'وصف مراجع']]]);
    $stayBefore = $stay->draft;

    $this->artisan('website:import-redesign')->assertSuccessful();

    $this->assertDatabaseCount('accommodations', 6);
    expect($profile->fresh()->published)->toEqual($profileBefore);
    expect($profile->fresh()->draft['requests_enabled'])->toBeTrue();
    expect($stay->fresh()->draft['description'])->toEqual($stayBefore['description']);
    expect($stay->fresh()->draft['capacity'])->toBe(7);
    expect($stay->fresh()->draft['facts_approved'])->toBeFalse();
    expect(MediaAsset::query()->whereNotNull('published_at')->count())->toBe(0);
    $image = MediaAsset::query()->where('draft->source_key', 'hero-pool2')->sole();
    expect($image->processing_status)->toBe('ready');
    Storage::disk('local')->assertExists($image->variants[1600]['path']);
    $profileDraft = $profile->fresh()->draft;
    $profileDraft['redesign']['pages']['home']['title']['en'] = 'Staff edited heading';
    $profile->update(['draft' => $profileDraft]);
    $before = $profile->getAttributes();
    $mediaCount = MediaAsset::query()->count();
    $stayAttributes = $stay->fresh()->getAttributes();

    $this->artisan('website:import-redesign')->assertSuccessful();

    expect($profile->fresh()->getAttributes())->toEqual($before);
    expect($stay->fresh()->getAttributes())->toEqual($stayAttributes);
    expect(MediaAsset::query()->count())->toBe($mediaCount);
    Queue::assertNothingPushed();
});

test('gallery categories exclude artwork and unpublished images while preserving page associations', function () {
    Queue::fake();
    $profile = prepareWebsiteRedesign();
    $image = MediaAsset::factory()->ready()->create();
    $image->update(['draft' => [...$image->draft, 'category' => 'sport', 'gallery_visible' => false]]);
    $image->publish();
    $profile->update(['draft' => [...$profile->draft, 'redesign' => [...$profile->draft['redesign'], 'media' => ['hall' => $image->id]]]]);
    $profile->publish();

    $content = app(WebsiteData::class)->forLocale('en');

    expect($content['media'])->toBeEmpty();
    expect($content['redesign']['images']['hall']['category'])->toBe('sport');
});

test('new discovery pages require published content and retain equivalent language routes', function (string $page) {
    $this->get('/en/'.$page)->assertNotFound();
    $profile = prepareWebsiteRedesign();
    $source = WebsiteRedesign::source();
    $key = $page === 'school-trips' ? 'schools' : $page;
    $profile->update(['draft' => [...$profile->draft, 'redesign' => [...$profile->draft['redesign'], 'pages' => [$key => $source['pages'][$key]]]]]);
    $profile->publish();

    $this->get('/en/'.$page)->assertInertia(fn (Assert $response) => $response
        ->component('public/Redesign')->where('alternateUrl', url('/ar/'.$page))->has('redesign.pages.'.$key));
    $this->get('/ar/'.$page)->assertHeader('Content-Language', 'ar');
    $draft = $profile->draft;
    $draft['redesign']['pages'][$key]['enabled'] = false;
    $profile->update(['draft' => $draft]);
    $profile->publish();
    $this->get('/en/'.$page)->assertNotFound();
})->with(['experiences', 'dining', 'sport', 'school-trips']);

test('staff can save bilingual v2 page text without changing the published snapshot', function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort');
    $profile = prepareWebsiteRedesign();
    $published = $profile->published;
    $draft = $profile->draft;
    $draft['redesign']['pages']['home']['title'] = ['en' => 'A staff-edited welcome', 'ar' => 'ترحيب حرره الموظفون'];

    Livewire::test(ManageResortProfiles::class)
        ->callAction(TestAction::make('edit')->table($profile), ['draft' => $draft])
        ->assertHasNoActionErrors();

    expect($profile->fresh()->draft['redesign']['pages']['home']['title']['en'])->toBe('A staff-edited welcome');
    expect($profile->fresh()->draft['redesign']['pages']['home']['title']['ar'])->toBe('ترحيب حرره الموظفون');
    expect($profile->fresh()->published)->toEqual($published);
});

test('staff can add published photos to the v2 gallery without changing the current publication', function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Queue::fake();
    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort');
    $profile = prepareWebsiteRedesign();
    $image = MediaAsset::factory()->ready()->create();
    $image->publish();
    $published = $profile->published;
    $draft = $profile->draft;
    $draft['redesign']['gallery_media_ids'] = [$image->id];

    Livewire::test(ManageResortProfiles::class)
        ->callAction(TestAction::make('edit')->table($profile), ['draft' => $draft])
        ->assertHasNoActionErrors();

    expect($profile->fresh()->draft['redesign']['gallery_media_ids'])->toEqual([$image->id]);
    expect($profile->fresh()->published)->toEqual($published);
    Livewire::test(ManageResortProfiles::class)->assertActionExists(TestAction::make('publish')->table($profile))->callAction(TestAction::make('publish')->table($profile))->assertStatus(200)->assertHasNoActionErrors()->assertNotified('Published in both languages');
    expect(app(WebsiteData::class)->forLocale('en')['redesign']['gallery'][0]['id'])->toBe($image->id);
});

test('gallery selections preserve order and exclude images removed from gallery publication', function () {
    Queue::fake();
    $profile = prepareWebsiteRedesign();
    $first = MediaAsset::factory()->ready()->create();
    $second = MediaAsset::factory()->ready()->create();
    $first->publish();
    $second->publish();
    $profile->update(['draft' => [...$profile->draft, 'redesign' => [...$profile->draft['redesign'], 'gallery_media_ids' => [$second->id, $first->id]]]]);
    $profile->publish();

    expect(array_column(app(WebsiteData::class)->forLocale('en')['redesign']['gallery'], 'id'))->toEqual([$second->id, $first->id]);

    $second->update(['draft' => [...$second->draft, 'gallery_visible' => false]]);
    $second->publish();
    expect(array_column(app(WebsiteData::class)->forLocale('ar')['redesign']['gallery'], 'id'))->toEqual([$first->id]);
    $first->unpublish();
    expect(app(WebsiteData::class)->forLocale('en')['redesign']['gallery'])->toBeEmpty();
});

test('profiles without an explicit gallery selection retain the supplied photograph order', function () {
    $keys = array_slice(WebsiteRedesign::source()['gallery'], 0, 2);
    $content = WebsiteRedesign::forLocale(['redesign' => [
        'media' => [$keys[1] => 2, $keys[0] => 1],
    ]], [1 => ['id' => 1], 2 => ['id' => 2]], 'en');

    expect(array_column($content['gallery'], 'id'))->toEqual([1, 2]);
});

test('accommodation photographs retain staff selection order rather than the global gallery order', function () {
    Queue::fake();
    $first = MediaAsset::factory()->ready()->create();
    $second = MediaAsset::factory()->ready()->create();
    $first->publish();
    $second->publish();
    $stay = Accommodation::factory()->create();
    $presentation = array_intersect_key(WebsiteRedesign::source()['accommodations']['oman-villa'], array_flip(['page', 'title', 'description', 'overview', 'inclusions', 'badge', 'facts', 'categories']));
    $stay->update(['draft' => [...$stay->draft, 'media_ids' => [$first->id], 'redesign' => [...$presentation, 'media_ids' => [$second->id, $first->id]]]]);
    $stay->publish();

    $this->get('/en/accommodation/'.$stay->slug)->assertInertia(fn (Assert $page) => $page
        ->where('stay.images.0.id', $second->id)
        ->where('stay.images.1.id', $first->id));
});

test('filling missing resort information preserves staff text and resets approval for added contacts', function () {
    Storage::fake('local');
    Queue::fake();
    $profile = ResortProfile::factory()->published()->create();
    $profile->update(['draft' => [...$profile->draft, 'address' => ['en' => 'Staff-confirmed address', 'ar' => 'عنوان أكده الموظفون'], 'phone' => '', 'email' => '', 'whatsapp_number' => '']]);
    $published = $profile->published;

    $this->artisan('website:import-redesign', ['--fill-missing' => true])->assertSuccessful();

    expect($profile->fresh()->draft['address'])->toEqual(['en' => 'Staff-confirmed address', 'ar' => 'عنوان أكده الموظفون']);
    expect($profile->fresh()->draft['phone'])->toBe('+96890657840');
    expect($profile->fresh()->draft['email'])->toBe('reservations@jauharat.com');
    expect($profile->fresh()->draft['contacts_verified'])->toBeFalse();
    expect($profile->fresh()->draft['whatsapp_verified'])->toBeFalse();
    expect($profile->fresh()->published)->toEqual($published);
});

test('missing occupancy uses documented management values without inventing child allowances', function () {
    Storage::fake('local');
    Queue::fake();
    $majan = Accommodation::factory()->create(['slug' => 'majan-villa']);
    $majan->update(['draft' => [...$majan->draft, 'capacity' => null]]);

    $this->artisan('website:import-redesign', ['--fill-missing' => true])->assertSuccessful();

    expect($majan->fresh()->draft['capacity'])->toBe(6);
    expect($majan->fresh()->draft['capacity_basis'])->toBe('adults');
    expect($majan->fresh()->published)->toBeNull();
    expect(Accommodation::query()->where('slug', 'salalah-villa')->sole()->draft['capacity'])->toBe(8);
    expect(Accommodation::query()->where('slug', 'standard-room')->sole()->draft['capacity_basis'])->toBe('guests');
});
