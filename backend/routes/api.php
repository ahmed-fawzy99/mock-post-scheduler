<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/reset-password', 'resetPassword');
    Route::post('/resend-verification', 'resendVerificationEmail')->middleware(['auth:sanctum', 'throttle:6,1']);
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');

    Route::patch('/update-info', 'updateUserInfo')->middleware('auth:sanctum');
    Route::patch('/update-password', 'updateUserPassword')->middleware('auth:sanctum');
    Route::get('/user', 'getAuthUser')->middleware('auth:sanctum');
});
