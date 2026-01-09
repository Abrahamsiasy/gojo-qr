<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QrCodeController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [QrCodeController::class, 'show']);

