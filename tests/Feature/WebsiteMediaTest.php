<?php

use App\Actions\Website\ProcessImage;
use App\Jobs\ProcessWebsiteImage;
use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    Queue::fake();
    Storage::fake('local');
});

test('processing creates bounded responsive variants and keeps originals private', function () {
    $path = UploadedFile::fake()->image('source.png', 1800, 1200)->store('website/originals', 'local');
    $asset = MediaAsset::factory()->create(['original_path' => $path]);
    app(ProcessImage::class)->handle($asset);
    expect($asset->processing_status)->toBe('ready')->and($asset->variants[1600]['width'])->toBe(1600);
    Storage::disk('local')->assertExists($path);
    foreach ($asset->variants as $variant) {
        Storage::disk('local')->assertExists($variant['path']);
    }
    $params = ['asset' => $asset->id, 'size' => 960, 'version' => substr(hash('sha256', $asset->variants[960]['path']), 0, 16)];
    $this->get(route('website.media', $params))->assertNotFound();
    $asset->publish();
    $this->get(route('website.media', $params))->assertOk()->assertHeader('Content-Type', 'image/webp')->assertHeader('X-Content-Type-Options', 'nosniff');
    $asset->unpublish();
    $this->get(route('website.media', $params))->assertNotFound();
});

test('signed draft images still require a staff session', function () {
    $asset = MediaAsset::factory()->ready()->create();
    Storage::disk('local')->put($asset->variants[960]['path'], 'test');
    $url = URL::temporarySignedRoute('website.media', now()->addMinute(), ['asset' => $asset->id, 'size' => 960, 'version' => substr(hash('sha256', $asset->variants[960]['path']), 0, 16), 'preview' => 1]);
    $this->get($url)->assertNotFound();
    $this->actingAs(User::factory()->create(['is_staff' => true]), 'resort')->get($url)->assertOk()->assertHeader('Cache-Control', 'no-store, private');
    $this->get($url.'&extra=1')->assertNotFound();
});

test('image replacement keeps the published revision until processing and approval', function () {
    $path = UploadedFile::fake()->image('one.png')->store('website/originals', 'local');
    $asset = MediaAsset::factory()->create(['original_path' => $path]);
    app(ProcessImage::class)->handle($asset);
    $asset->publish();
    $published = $asset->published;
    $replacement = UploadedFile::fake()->image('two.png')->store('website/originals', 'local');
    $asset->update(['original_path' => $replacement]);
    expect($asset->fresh()->published)->toBe($published)->and($asset->fresh()->processing_status)->toBe('pending')->and($asset->fresh()->variants)->toBeNull();
    Queue::assertPushed(ProcessWebsiteImage::class, 2);
});

test('invalid images fail without becoming publishable', function () {
    Storage::disk('local')->put('website/originals/invalid.png', '<svg><script>alert(1)</script></svg>');
    $asset = MediaAsset::factory()->create(['original_path' => 'website/originals/invalid.png']);
    expect(fn () => (new ProcessWebsiteImage($asset->id))->handle(app(ProcessImage::class)))->toThrow(RuntimeException::class);
    expect($asset->fresh()->processing_status)->toBe('failed')->and($asset->fresh()->published)->toBeNull();
});

test('responsive artwork preserves transparent pixels against dark backgrounds', function () {
    $image = imagecreatetruecolor(20, 20);
    imagealphablending($image, false);
    imagesavealpha($image, true);
    imagefill($image, 0, 0, imagecolorallocatealpha($image, 255, 255, 255, 127));
    imagefilledrectangle($image, 8, 8, 12, 12, imagecolorallocatealpha($image, 255, 255, 255, 0));
    ob_start();
    imagepng($image);
    $bytes = ob_get_clean();
    Storage::disk('local')->put('website/originals/artwork.png', $bytes);
    $asset = MediaAsset::factory()->create(['original_path' => 'website/originals/artwork.png']);

    app(ProcessImage::class)->handle($asset);

    $variant = imagecreatefromstring(Storage::disk('local')->get($asset->variants[960]['path']));
    expect(imagecolorsforindex($variant, imagecolorat($variant, 0, 0))['alpha'])->toBe(127);
    expect(imagecolorsforindex($variant, imagecolorat($variant, 10, 10))['alpha'])->toBe(0);
});
