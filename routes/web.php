<?php

use Illuminate\Support\Facades\Route;

Route::get('/docs/openapi.json', fn () => response()->file(storage_path('api-docs/api-docs.json')));
Route::get('/docs', fn () => view('swagger'));
