<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return response()->json([
        'success' => false,
        'message' => 'Non authentifié, veuillez vous connecter.'
    ],401);
})->name('login');

Route::get('/', function(){
    return view('welcome');
});
