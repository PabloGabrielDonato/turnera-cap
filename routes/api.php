<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('locations', \App\Http\Controllers\LocationApiController::class);
Route::post('locations/{id}/time-slots', [\App\Http\Controllers\LocationApiController::class, 'addTimeSlot'])->name('locations.time-slots.add');
Route::get('locations/{id}/availability',[\App\Http\Controllers\LocationApiController::class, 'getAvailability'])->name('locations.availability.get');
Route::apiResource('bookings', \App\Http\Controllers\BookingApiController::class);

