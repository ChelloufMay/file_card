<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DeckController;

Route::post('register', [AuthController::class, 'register']);   // optional
Route::post('login', [AuthController::class, 'login']);         // this fixes your missing route

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'me']);
    // Add your API routes that need auth here, e.g. GET /api/decks
});
Route::get('/decks', [DeckController::class, 'index']);

