<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DeckController;

use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\ReviewController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);   // optional
Route::post('login', [AuthController::class, 'login']);         // this fixes your missing route

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'me']);
    Route::apiResource('decks', DeckController::class);
    // Add your API routes that need auth here, e.g. GET /api/decks

    // Cards per deck + card operations
    Route::get('decks/{deck}/cards', [CardController::class, 'index']);
    Route::apiResource('cards', CardController::class)->except(['index']);

    // Reviews / SRS
    Route::get('reviews/due', [ReviewController::class, 'due']);
    Route::post('cards/{card}/review', [ReviewController::class, 'review']);
});

//Route::get('/decks', [DeckController::class, 'index']);

