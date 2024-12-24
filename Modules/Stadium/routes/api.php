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

Route::group(['middleware' => 'api'], function () {
    Route::name('api.')->group(function () {
        Route::controller(StadiumController::class)
            ->prefix('/stadium')->name('stadium.')->group(function () {
                Route::get('/list', 'list');
                Route::post('/store', 'store');
                Route::post('/update', 'update');
                Route::get('/show/{id}', 'show');
                Route::delete('/delete/{id}', 'delete');
            });
        Route::controller(PitchController::class)
            ->prefix('/pitch')->name('pitch.')->group(function () {
                Route::get('/list', 'list');
                Route::post('/store', 'store');
                Route::post('/update', 'update');
                Route::get('/show/{id}', 'show');
                Route::delete('/delete/{id}', 'delete');
            });
    });
});
