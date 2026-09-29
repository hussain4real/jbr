<?php

use App\Filament\Resources\ResortProfiles\Pages\ManageResortProfiles;
use App\Jobs\ProcessWebsiteImage;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;

beforeEach(function () {
    Queue::fake([ProcessWebsiteImage::class]);
    Storage::fake('local');
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function prepareResortPromotion(): ResortProfile
{
    $poster = MediaAsset::factory()->ready()->published()->create();
    Storage::disk('local')->put('website/promotions/brochure.pdf', "%PDF-1.4\noriginal brochure\n%%EOF");
    $profile = ResortProfile::factory()->published()->create();
    $profile->update(['draft' => [...$profile->draft,
        'promotion_enabled' => true,
        'promotion_title' => ['en' => 'Discover the resort', 'ar' => 'اكتشف المنتجع'],
        'promotion_description' => ['en' => 'Our resort brochure.', 'ar' => 'كتيّب المنتجع.'],
        'promotion_media_id' => $poster->id,
        'promotion_pdf_path' => 'website/promotions/brochure.pdf',
    ]]);

    return $profile;
}

test('a promotion appears in both languages only after publication and its poster is not a property photograph', function () {
    $profile = prepareResortPromotion();
    $url = route('website.promotion', ['profile' => $profile, 'version' => substr(hash('sha256', $profile->draft['promotion_pdf_path']), 0, 16)]);
    $this->get('/en')->assertInertia(fn (Assert $page) => $page->where('promotion', null));
    $this->get($url)->assertNotFound();

    $profile->publish();

    $this->get('/en')->assertInertia(fn (Assert $page) => $page
        ->where('promotion.title', 'Discover the resort')->where('promotion.image.id', $profile->draft['promotion_media_id'])
        ->where('promotion.downloadUrl', $url)->has('media', 0)->where('hero', null));
    $this->get('/ar')->assertInertia(fn (Assert $page) => $page->where('promotion.title', 'اكتشف المنتجع')->where('promotion.description', 'كتيّب المنتجع.'));
    $this->get($url)->assertDownload('jawharat-bidiyah-resort-promotion.pdf')
        ->assertHeader('Content-Type', 'application/pdf')->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeader('Cache-Control', 'no-store, private');
});

test('staff can upload a replacement PDF and edit promotion copy without changing the published version', function () {
    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort');
    $profile = prepareResortPromotion();
    $profile->publish();
    $published = $profile->published;
    $publishedUrl = route('website.promotion', ['profile' => $profile, 'version' => substr(hash('sha256', $published['promotion_pdf_path']), 0, 16)]);
    $draft = [...$profile->draft, 'promotion_title' => ['en' => 'New brochure', 'ar' => 'كتيّب جديد'],
        'promotion_pdf_path' => [UploadedFile::fake()->createWithContent('new.pdf', "%PDF-1.4\nnew brochure\n%%EOF")]];

    Livewire::test(ManageResortProfiles::class)
        ->callAction(TestAction::make('edit')->table($profile), ['draft' => $draft])->assertHasNoActionErrors();

    expect($profile->fresh()->published)->toBe($published);
    $replacementPath = $profile->fresh()->draft['promotion_pdf_path'];
    expect($replacementPath)->not->toBe($published['promotion_pdf_path']);
    Storage::disk('local')->assertExists($replacementPath);
    $replacementUrl = route('website.promotion', ['profile' => $profile, 'version' => substr(hash('sha256', $replacementPath), 0, 16)]);
    $this->get($publishedUrl)->assertDownload('jawharat-bidiyah-resort-promotion.pdf');
    $this->get($replacementUrl)->assertNotFound();
    $this->get('/en')->assertInertia(fn (Assert $page) => $page->where('promotion.title', 'Discover the resort'));
    Livewire::test(ManageResortProfiles::class)->callAction(TestAction::make('publish')->table($profile))->assertNotified('Published in both languages');
    $this->get('/en')->assertInertia(fn (Assert $page) => $page->where('promotion.title', 'New brochure'));
    $this->get($replacementUrl)->assertDownload('jawharat-bidiyah-resort-promotion.pdf');
    $this->get($publishedUrl)->assertNotFound();
});

test('staff cannot replace the brochure by submitting the path of a different existing file', function () {
    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort');
    $profile = prepareResortPromotion();
    $originalDraft = $profile->draft;
    Storage::disk('local')->put('website/promotions/another.pdf', "%PDF-1.4\nanother brochure\n%%EOF");

    Livewire::test(ManageResortProfiles::class)
        ->callAction(TestAction::make('edit')->table($profile), ['draft' => [...$originalDraft, 'promotion_pdf_path' => ['website/promotions/another.pdf']]])
        ->assertHasActionErrors(['draft.promotion_pdf_path']);

    expect($profile->fresh()->draft)->toBe($originalDraft);
});

test('disabling a published promotion removes the public feature and rejects its existing download URL', function () {
    $profile = prepareResortPromotion();
    $profile->publish();
    $url = route('website.promotion', ['profile' => $profile, 'version' => substr(hash('sha256', $profile->draft['promotion_pdf_path']), 0, 16)]);

    $profile->update(['draft' => [...$profile->draft, 'promotion_enabled' => false]]);
    $profile->publish();

    $this->get('/en')->assertInertia(fn (Assert $page) => $page->where('promotion', null));
    $this->get($url)->assertNotFound();
});

test('incomplete promotions cannot replace approved website content', function (string $field, mixed $value) {
    $profile = prepareResortPromotion();
    $published = $profile->published;
    $draft = $profile->draft;
    data_set($draft, $field, $value);
    $profile->update(['draft' => $draft]);

    expect(fn () => $profile->publish())->toThrow(ValidationException::class);
    expect($profile->fresh()->published)->toBe($published);
})->with([
    'missing Arabic heading' => ['promotion_title.ar', null],
    'missing Arabic description' => ['promotion_description.ar', null],
    'missing poster' => ['promotion_media_id', null],
    'missing PDF' => ['promotion_pdf_path', null],
    'another private directory' => ['promotion_pdf_path', 'private-document.pdf'],
    'parent directory traversal' => ['promotion_pdf_path', 'website/promotions/../../private.pdf'],
]);

test('publication rejects unpublished poster images and files that are not PDFs', function () {
    $profile = prepareResortPromotion();
    MediaAsset::findOrFail($profile->draft['promotion_media_id'])->unpublish();
    expect(fn () => $profile->publish())->toThrow(ValidationException::class);
    MediaAsset::findOrFail($profile->draft['promotion_media_id'])->publish();
    Storage::disk('local')->put($profile->draft['promotion_pdf_path'], '<html>Not a PDF</html>');

    expect(fn () => $profile->publish())->toThrow(ValidationException::class);
});

test('draft brochure downloads require a valid signature and a resort staff session', function () {
    $profile = prepareResortPromotion();
    $url = URL::temporarySignedRoute('website.promotion', now()->addMinutes(10), ['profile' => $profile, 'version' => substr(hash('sha256', $profile->draft['promotion_pdf_path']), 0, 16), 'preview' => 1]);
    $this->get($url)->assertNotFound();
    $this->actingAs(User::factory()->create(['is_staff' => false]), 'resort')->get($url)->assertNotFound();

    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort')->get($url)
        ->assertDownload('jawharat-bidiyah-resort-promotion.pdf');
    $this->get($url.'&extra=1')->assertNotFound();
});

test('missing published PDFs hide the promotion and stale document versions cannot be downloaded', function () {
    $profile = prepareResortPromotion();
    $profile->publish();
    $this->get(route('website.promotion', ['profile' => $profile, 'version' => '0000000000000000']))->assertNotFound();
    Storage::disk('local')->delete($profile->draft['promotion_pdf_path']);

    $this->get('/en')->assertInertia(fn (Assert $page) => $page->where('promotion', null));
});
