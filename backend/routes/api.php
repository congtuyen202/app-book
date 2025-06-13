<?php

use App\Http\Controllers\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController; // Import Controller
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Routes cho BookController
Route::apiResource('books', BookController::class);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('books', BookController::class);
    Route::post('/books', [BookController::class, 'store'])->middleware('permission:create-book');
    Route::put('/books/{id}', [BookController::class, 'update'])->middleware('permission:edit-book');
    Route::delete('/books/{id}', [BookController::class, 'destroy'])->middleware('permission:delete-book');

    Route::apiResource('reviews', ReviewController::class);
    Route::post('/reviews', [ReviewController::class, 'store'])->middleware('permission:create-review');
    // ... và nhiều middleware khác tùy theo logic phân quyền của bạn
});

// Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    // ... các route quản trị khác
});
