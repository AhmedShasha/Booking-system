<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\AvailabilityController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Services routes
    Route::apiResource('services', ServiceController::class)
        ->except(['index', 'show']);
    
    // Public services routes
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{service}', [ServiceController::class, 'show']);
    
    // Availabilities routes
    Route::apiResource('availabilities', AvailabilityController::class);
    
    // Bookings routes
    Route::middleware(['throttle:booking'])->group(function () {
        Route::post('/bookings', [BookingController::class, 'store']);
        Route::get('/services/{service}/available-slots', [BookingController::class, 'getAvailableSlots']);
        Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);
    });
    Route::apiResource('bookings', BookingController::class)
        ->only(['index', 'show', 'update', 'destroy']);
    Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm']);
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    
    // Reports routes
    Route::prefix('reports')->group(function () {
        Route::get('bookings', [ReportController::class, 'bookings']);
        Route::get('cancellation-rates', [ReportController::class, 'cancellationRates']);
        Route::get('peak-hours', [ReportController::class, 'peakHours']);
        Route::get('average-duration', [ReportController::class, 'averageDuration']);
    });
});