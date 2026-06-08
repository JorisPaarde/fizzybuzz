<?php

use App\Http\Controllers\PriceImportController;
use App\Http\Controllers\PriceSubmissionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

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
});

require __DIR__.'/auth.php';
