<?php

use Illuminate\Support\Facades\Route;

Route::get('/availableSlots/{pitchId}', [\App\Http\Controllers\BookingController::class, 'availableSlots']);
Route::get('/bookPitch', [\App\Http\Controllers\BookingController::class, 'bookPitch']);
