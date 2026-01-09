<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrCodeController;

Route::get('/', [QrCodeController::class, 'index']);
Route::post('/generate', [QrCodeController::class, 'generate'])->name('qr.generate');

