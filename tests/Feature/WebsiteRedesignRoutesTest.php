<?php

use App\Actions\Website\WebsiteRedesign;
use App\Models\ResortProfile;
use Illuminate\Support\Facades\Vite;
use Inertia\Testing\AssertableInertia;

test('static redesign links redirect into the existing localized Laravel routes', function (string $source, string $destination) {
    $this->get('/'.$source.'.html')->assertRedirect('/en'.$destination)->assertStatus(301);
    $this->get('/ar/'.$source.'.html')->assertRedirect('/ar'.$destination)->assertStatus(301);
})->with([
    'homepage' => ['index', ''],
    'villas' => ['villas', '/accommodation'],
    'experiences' => ['experiences', '/experiences'],
    'dining' => ['dining', '/dining'],
    'sport' => ['sport', '/sport'],
    'schools' => ['school-trips', '/school-trips'],
    'gallery' => ['gallery', '/gallery'],
    'location' => ['location', '/contact'],
    'Oman Villa' => ['villa-oman', '/accommodation/oman-villa'],
    'Majan Villa' => ['villa-majan', '/accommodation/majan-villa'],
    'Salalah Villa' => ['villa-salalah', '/accommodation/salalah-villa'],
    'Mazon Villa' => ['villa-mazon', '/accommodation/mazon-villa'],
    'luxury villa' => ['villa-luxury-one-bedroom', '/accommodation/luxury-1-br-villa'],
    'standard room' => ['standard-room', '/accommodation/standard-room'],
]);

test('sitemap lists only enabled published discovery pages in both languages', function () {
    config(['website.indexable' => true]);
    $profile = ResortProfile::factory()->published()->create();
    $source = WebsiteRedesign::source();
    $profile->update(['draft' => [...$profile->draft, 'redesign' => [
        'revision' => $source['revision'], 'facts_approved' => true, 'arabic_reviewed' => true, 'media' => [],
        'pages' => ['experiences' => $source['pages']['experiences'], 'dining' => [...$source['pages']['dining'], 'enabled' => false]],
    ]]]);

    $this->get('/sitemap.xml')->assertDontSee('/experiences');
    $profile->publish();

    $this->get('/sitemap.xml')->assertSee('/en/experiences')->assertSee('/ar/experiences')->assertDontSee('/en/dining')->assertDontSee('/ar/dining')->assertDontSee('/school-trips');
});

test('disabled redesign home pages fall back to the existing published website', function () {
    $profile = ResortProfile::factory()->published()->create();
    $source = WebsiteRedesign::source();
    $profile->update(['draft' => [...$profile->draft, 'redesign' => [
        'revision' => $source['revision'], 'facts_approved' => true, 'arabic_reviewed' => true, 'media' => [],
        'pages' => ['home' => [...$source['pages']['home'], 'enabled' => false]],
    ]]]);
    $profile->publish();

    $this->get('/en')->assertInertia(fn (AssertableInertia $page) => $page->component('public/Website'));
});

test('compiled local previews leave the running development server intact and cannot enable drafts in production', function () {
    $hotFile = tempnam(sys_get_temp_dir(), 'jbr-vite-');
    file_put_contents($hotFile, 'http://vite.example.invalid:5173');
    Vite::useHotFile($hotFile);
    ResortProfile::factory()->create();
    config(['website.local_preview' => true, 'website.preview_build' => true]);
    app()->detectEnvironment(fn () => 'local');

    try {
        $this->get('/en')->assertSee('/build/assets/')->assertDontSee('vite.example.invalid');
        expect(file_get_contents($hotFile))->toBe('http://vite.example.invalid:5173');
        app()->detectEnvironment(fn () => 'production');
        Vite::useHotFile($hotFile);
        $this->get('/en')->assertSee('vite.example.invalid')->assertInertia(fn (AssertableInertia $page) => $page->where('isPreview', false));
    } finally {
        unlink($hotFile);
    }
});
