<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrCodeController;

Route::get('/', [QrCodeController::class, 'index']);

// Rate limit QR generation: 60 requests per minute per IP
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/generate', [QrCodeController::class, 'generate'])->name('qr.generate');
});

