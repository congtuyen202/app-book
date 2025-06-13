<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::prefix('/')->group(function () {
    Route::get('{any?}', [HomeController::class, 'index'])->where('any', '.*')->name('admin');
});
