<?php

use App\Actions\GetFeaturedApartments;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

Route::get('/', function (GetFeaturedApartments $getFeaturedApartments) {
    $featuredApartments = $getFeaturedApartments();
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'featuredApartments' => $featuredApartments,
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::resource('apartments', \App\Http\Controllers\ApartmentController::class)
->except(['edit', 'update', 'destroy']);

Route::get('/payment-form', [PaymentController::class, 'showForm'])->name('payment-form');
Route::post('/initiate-transaction', [PaymentController::class, 'initiateTransaction'])->name('initiate-transaction');
Route::post('/payment-response', [PaymentController::class, 'handleResponse'])->name('payment-response');
//Route::post('/payment/success', [PaymentController::class, 'handleSuccess'])->name('payment.success');
Route::post('/payment/cancel', [PaymentController::class, 'handleCancel'])->name('payment.cancel');

require __DIR__.'/auth.php';
