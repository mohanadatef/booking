<?php

use Illuminate\Support\Facades\Route;

Route::get('/availableSlots', 'BookingController@availableSlots');
Route::get('/bookPitch', 'BookingController@bookPitch');
