<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify/otp', [AuthController::class, 'verify_otp']);
Route::post('/resend/otp', [AuthController::class, 'resend_otp']);
Route::post('/login', [AuthController::class, 'login']);
