<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CvController;
use App\Http\Controllers\API\CoverController;
use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\EventController;

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
});

// Protected Routes
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Cover letter Routes
Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('cover', CoverController::class);
});

// CV Routes
Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('cv', CvController::class);
});

// Application Routes
Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('application', ApplicationController::class);
});

// Event Routes
Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('event', EventController::class);
});
