<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('/health', [\App\Http\Controllers\Api\HealthController::class, 'index']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
    
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/ip-addresses', [\App\Http\Controllers\Api\IpAddressController::class, 'index']);
    Route::post('/ip-addresses', [\App\Http\Controllers\Api\IpAddressController::class, 'store']);
    Route::put('/ip-addresses/{id}', [\App\Http\Controllers\Api\IpAddressController::class, 'update']);
    Route::get('/ip-addresses/{id}', [\App\Http\Controllers\Api\IpAddressController::class, 'show']);
    
    Route::get('/audit-logs', [\App\Http\Controllers\Api\AuditLogController::class, 'index']);
    Route::get('/audit-logs/login', [\App\Http\Controllers\Api\AuditLogController::class, 'getLoginAuditLogs']);
    Route::get('/audit-logs/my-logins', [\App\Http\Controllers\Api\AuditLogController::class, 'getUserLoginAuditLogs']);
    Route::get('/audit-logs/ip-address/{id}', [\App\Http\Controllers\Api\AuditLogController::class, 'getIpAddressChangeHistory']);
});

