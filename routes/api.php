<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DeviceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Rutas públicas.
    Route::post('/login', [AuthController::class, 'login']);

    // Rutas Privadas (Requieren Token Sanctum).
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/devices', [DeviceController::class, 'index']);
        Route::get('/devices/{id}/telemetry', [DeviceController::class, 'telemetry']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
