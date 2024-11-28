<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// user routes
Route::prefix("/auth")->group(function () {
    Route::post('/register', [AuthController::class,'register'])->name('api.auth.register');
    Route::post('/login', [AuthController::class,'login'])->name('api.auth.login');
    Route::post('/logout', [AuthController::class,'logout'])
    ->middleware('auth:sanctum')
    ->name('api.auth.logout');    
});

// Public routes
Route::get('locations/{id}/availability',[\App\Http\Controllers\LocationApiController::class, 'getAvailability'])->name('locations.availability.get');
Route::apiResource('locations', \App\Http\Controllers\LocationApiController::class)
->except('destroy','store','update');

Route::apiResource('bookings', \App\Http\Controllers\BookingApiController::class);

// protected routes
Route::middleware('auth:sanctum')->group(function () {
    // routes to protect
});