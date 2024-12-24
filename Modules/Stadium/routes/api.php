<?php

use Illuminate\Support\Facades\Route;
use Modules\Stadium\Http\Controllers\StadiumController;
use Modules\Stadium\Http\Controllers\PitchController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

// Grouping the API routes and applying the 'api' middleware
Route::group(['middleware' => 'api'], function () {
    // Registering the routes for the Stadium resource
    Route::controller(StadiumController::class)
        ->prefix('/stadium')->name('stadium.')->group(function () {
            Route::get('/list', 'list'); // Fetches the list of stadiums
            Route::post('/store', 'store'); // Stores a new stadium
            Route::post('/update/{id}', 'update'); // Updates an existing stadium by ID
            Route::get('/show/{id}', 'show'); // Retrieves a specific stadium by ID
            Route::delete('/destroy/{id}', 'destroy'); // Deletes a stadium by ID
        });
    // Registering the routes for the Pitch resource
    Route::controller(PitchController::class)
        ->prefix('/pitch')->name('pitch.')->group(function () {
            Route::get('/list', 'list'); // Fetches the list of pitches
            Route::post('/store', 'store'); // Stores a new pitch
            Route::post('/update/{id}', 'update'); // Updates an existing pitch by ID
            Route::get('/show/{id}', 'show'); // Retrieves a specific pitch by ID
            Route::delete('/destroy/{id}', 'destroy'); // Deletes a pitch by ID
        });
});

