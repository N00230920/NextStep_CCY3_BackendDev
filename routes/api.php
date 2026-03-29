<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CvController;
use App\Http\Controllers\API\CoverController;
use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\EventController;

// Public Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
});

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Resources
    Route::get('/dashboard', [ApplicationController::class, 'dashboard']);
    Route::patch('/applications/{id}/status', [ApplicationController::class, 'updateStatus']);
    Route::get('/events/upcoming', [EventController::class, 'upcoming']);
    
    Route::apiResource('applications', ApplicationController::class);
    Route::apiResource('cvs', CvController::class);
    Route::apiResource('covers', CoverController::class);
    Route::apiResource('events', EventController::class);
});