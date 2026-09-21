<?php

use App\Models\Accommodation;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('photo imports add gallery drafts without duplicate accommodations or overwriting reviewed content', function () {
    Storage::fake('local');
    Queue::fake();
    $profile = ResortProfile::factory()->published()->create();
    $profileBefore = $profile->getAttributes();

    $this->artisan('website:import-photographs')->assertSuccessful();

    $this->assertDatabaseCount('media_assets', 10);
    $this->assertDatabaseCount('accommodations', 6);
    $galleryPhoto = MediaAsset::query()->where('original_path', 'website/originals/bathroom-washbasin.jpeg')->sole();
    expect($galleryPhoto->published)->toBeNull();
    expect($galleryPhoto->processing_status)->toBe('ready');
    Storage::disk('local')->assertExists($galleryPhoto->original_path);
    Storage::disk('local')->assertExists($galleryPhoto->variants[960]['path']);
    expect(Accommodation::query()->get()->flatMap(fn (Accommodation $record): array => $record->draft['media_ids'])->all())->not->toContain($galleryPhoto->id);

    $galleryPhoto->draft = [...$galleryPhoto->draft, 'caption' => ['en' => 'Staff-edited bathroom caption', 'ar' => 'تعليق الحمّام الذي عدّله الموظف'], 'rights_confirmed' => true, 'property_verified' => true, 'arabic_reviewed' => true];
    $galleryPhoto->save();
    $galleryPhoto->publish();
    $photoBefore = $galleryPhoto->getAttributes();

    $this->artisan('website:import-photographs')->assertSuccessful();

    $this->assertDatabaseCount('media_assets', 10);
    $this->assertDatabaseCount('accommodations', 6);
    expect($galleryPhoto->fresh()->getAttributes())->toEqual($photoBefore);
    expect($profile->fresh()->getAttributes())->toEqual($profileBefore);
    Queue::assertNothingPushed();
});
