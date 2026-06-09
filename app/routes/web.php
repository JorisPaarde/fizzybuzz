<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InboundEmailWebhookController;
use App\Http\Controllers\PriceComparisonController;
use App\Http\Controllers\MemberWholesalerController;
use App\Http\Controllers\PriceImportController;
use App\Http\Controllers\PriceSubmissionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/inbound-email', InboundEmailWebhookController::class)
    ->name('webhooks.inbound-email');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/compare', [PriceComparisonController::class, 'index'])->name('compare.index');
Route::get('/compare/{product}', [PriceComparisonController::class, 'show'])->name('compare.show');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/prices', [PriceSubmissionController::class, 'index'])->name('prices.index');
    Route::get('/prices/{submission}/edit', [PriceSubmissionController::class, 'edit'])->name('prices.edit');
    Route::put('/prices/{submission}', [PriceSubmissionController::class, 'update'])->name('prices.update');
    Route::delete('/prices/{submission}', [PriceSubmissionController::class, 'destroy'])->name('prices.destroy');

    Route::get('/prices/import/upload', [PriceImportController::class, 'create'])->name('prices.import.create');
    Route::post('/prices/import/upload', [PriceImportController::class, 'store'])->name('prices.import.store');
    Route::get('/prices/import/{import}/review', [PriceImportController::class, 'review'])->name('prices.import.review');
    Route::post('/prices/import/{import}/confirm', [PriceImportController::class, 'confirm'])->name('prices.import.confirm');
    Route::get('/prices/manual/create', [PriceImportController::class, 'manualCreate'])->name('prices.manual.create');
    Route::post('/prices/manual', [PriceImportController::class, 'manualStore'])->name('prices.manual.store');

    Route::get('/my-wholesalers', [MemberWholesalerController::class, 'index'])->name('wholesalers.index');
    Route::post('/my-wholesalers', [MemberWholesalerController::class, 'store'])->name('wholesalers.store');
    Route::delete('/my-wholesalers/{wholesaler}', [MemberWholesalerController::class, 'destroy'])->name('wholesalers.destroy');
});

require __DIR__.'/auth.php';
