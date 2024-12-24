<?php

// Importing necessary classes for routing
use Illuminate\Support\Facades\Route;
use Modules\Booking\Http\Controllers\BookingController;

// Defining a route group with 'api' middleware for booking-related routes
Route::group(['middleware' => 'api'], function () {
    // Setting up the BookingController with a route prefix and name
    Route::controller(BookingController::class)
        ->prefix('/booking')->name('booking.')->group(function () {
            // Route to fetch available booking slots
            Route::get('/availableSlots', 'availableSlots');
            // Route to store a new booking
            Route::post('/store', 'store');
        });
});
