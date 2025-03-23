<?php

use App\Http\Controllers\Api\V1\PlatformController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::get('platforms', [PlatformController::class, 'index'])->name('platforms.index');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('all-posts', [PostController::class, 'allIndex']);
    Route::resource('posts', PostController::class);
    Route::resource('platforms', PlatformController::class);
    Route::resource('users', UserController::class);
});

