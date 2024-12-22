<?php

use Illuminate\Support\Facades\Route;

Route::get('/availableSlots/{pitchId}', [\App\Http\Controllers\BookingController::class, 'availableSlots']);
Route::post('/bookPitch/{pitchId}', [\App\Http\Controllers\BookingController::class, 'bookPitch']);
