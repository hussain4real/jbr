<?php

use App\Http\Controllers\GuestRequestController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\WebsiteMediaController;
use App\Http\Middleware\SetWebsiteLocale;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/en')->name('home');
Route::get('/sitemap.xml', [WebsiteController::class, 'sitemap'])->name('website.sitemap');
Route::get('/website-media/{asset}/{size}/{version}', WebsiteMediaController::class)->whereNumber('asset')->whereIn('size', ['480', '960', '1600'])->name('website.media');

Route::prefix('{locale}')->where(['locale' => 'en|ar'])->middleware(SetWebsiteLocale::class)->name('website.')->group(function (): void {
    Route::get('/', [WebsiteController::class, 'page'])->defaults('page', 'home')->name('home');
    Route::get('/accommodation', [WebsiteController::class, 'page'])->defaults('page', 'accommodation')->name('accommodations');
    Route::get('/accommodation/{slug}', [WebsiteController::class, 'page'])->defaults('page', 'stay')->name('accommodation');
    Route::get('/gallery', [WebsiteController::class, 'page'])->defaults('page', 'gallery')->name('gallery');
    Route::get('/plan-your-stay', [WebsiteController::class, 'page'])->defaults('page', 'plan')->name('plan');
    Route::get('/contact', [WebsiteController::class, 'page'])->defaults('page', 'contact')->name('contact');
    Route::get('/request-a-booking', [WebsiteController::class, 'page'])->defaults('page', 'booking')->name('booking');
    Route::get('/privacy', [WebsiteController::class, 'page'])->defaults('page', 'privacy')->name('privacy');
    Route::get('/request-received', [WebsiteController::class, 'page'])->defaults('page', 'received')->name('received');
    Route::get('/preview/{type}/{record}', [WebsiteController::class, 'preview'])->middleware(['auth:resort', 'signed'])->whereNumber('record')->name('preview');
    Route::post('/booking-requests', [GuestRequestController::class, 'booking'])->middleware('throttle:guest-requests')->name('booking-requests');
    Route::post('/enquiries', [GuestRequestController::class, 'enquiry'])->middleware('throttle:guest-requests')->name('enquiries');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});
require __DIR__.'/settings.php';
