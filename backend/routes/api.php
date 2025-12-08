<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DeckController;

use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Auth flows
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

// verify login code and get token
Route::post('verify-login', [AuthController::class, 'verifyLogin'])->middleware('throttle:20,5');

// Password reset
Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:5,15');
Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:5,15');

// Contact form (public, rate limited to prevent spam)
Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:5,15');

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

    // resend verification if needed
    Route::post('email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent']);
    })->name('verification.send');
});


