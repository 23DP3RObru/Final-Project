<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;

// Test route
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now()->toDateTimeString(),
    ]);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toDateTimeString(),
    ]);
});

// Items API routes
Route::apiResource('items', ItemController::class);