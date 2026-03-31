<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('/health', [\App\Http\Controllers\Api\HealthController::class, 'index']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});
