<?php

use App\Models\Accommodation;
use App\Models\Faq;
use App\Models\MediaAsset;
use App\Models\ResortProfile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

test('draft edits preserve the public snapshot until explicitly published', function () {
    $faq = Faq::factory()->published()->create();
    $old = $faq->published;
    $faq->update(['draft' => [...$faq->draft, 'answer' => ['en' => 'Revised answer', 'ar' => 'إجابة معدلة']]]);
    expect($faq->fresh()->published)->toBe($old);
    $faq->publish();
    expect($faq->fresh()->published['answer']['en'])->toBe('Revised answer');
    $faq->unpublish();
    expect($faq->fresh()->published)->toBeNull()->and($faq->draft['answer']['en'])->toBe('Revised answer');
});

test('publication requires complete translations and explicit approvals', function () {
    $faq = Faq::factory()->create(['draft' => ['question' => ['en' => 'English only']]]);
    expect(fn () => $faq->publish())->toThrow(ValidationException::class);
    expect($faq->fresh()->published_at)->toBeNull();
});

test('accommodation publication requires approved media and confirmed details', function () {
    Queue::fake();
    $media = MediaAsset::factory()->ready()->create();
    $stay = Accommodation::factory()->create();
    $stay->update(['draft' => [...$stay->draft, 'media_ids' => [$media->id]]]);
    expect(fn () => $stay->publish())->toThrow(ValidationException::class);
    $media->publish();
    $stay->publish();
    expect($stay->fresh()->published['capacity'])->toBe(2);
    expect(fn () => $stay->update(['slug' => 'changed']))->toThrow(ValidationException::class);
});

test('accepting requests requires privacy retention contact and operating approval', function (string $field) {
    $profile = ResortProfile::factory()->create();
    $profile->update(['draft' => [...$profile->draft, $field => false]]);
    expect(fn () => $profile->publish())->toThrow(ValidationException::class);
    expect($profile->fresh()->published)->toBeNull();
})->with(['contacts_verified', 'privacy_approved', 'operations_approved', 'arabic_reviewed']);
