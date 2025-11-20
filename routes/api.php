<?php

use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvantageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route d’inscription marchand et client
Route::post('/register/marchand', [AuthController::class, 'register_marchand']);
Route::post('/register/client', [AuthController::class, 'register_client']);

//Verification et renvoi d’OTP marchand
Route::post('/verify/otp', [AuthController::class, 'verify_otp']);
Route::post('/resend/otp', [AuthController::class, 'resend_otp']);

//Connexion marchand et client
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:marchand')->group(function(){
    //Route pour afficher les infos du marchand
    Route::get('/info/profil/marchand', [AuthController::class, 'info_profil_marchand']);
    //Route pour modifier les infos du marchand
    Route::post('/update/profil/marchand', [AuthController::class, 'update_profil_marchand']);
    //Route pour modifier le mot de passe du marchand
    Route::post('/update/password/marchand', [AuthController::class, 'update_password_marchand']);
});

Route::middleware('auth:client')->group(function(){
    //Route pour afficher les infos du client
    Route::get('/info/profil/client', [AuthController::class, 'info_profil_client']);
    //Route pour modifier les infos du client
    Route::post('/update/profil/client', [AuthController::class, 'update_profil_client']);
    //Route pour modifier le mot de passe du client
    Route::post('/update/password/client', [AuthController::class, 'update_password_client']);
});

//Connexion Admin
Route::post('/login/admin', [AuthController::class, 'login_admin']);

//Avantage
Route::middleware('auth:admin')->group(function(){
    Route::post('/ajout/avantage', [AvantageController::class, 'ajout_avantage']);
    Route::post('/update/avantage/{id}', [AvantageController::class, 'update_avantage']);
    Route::post('/delete/avantage/{id}', [AvantageController::class, 'delete_avantage']);
});
//Liste des avantages
Route::get('/avantages', [AvantageController::class, 'liste_avantage']);

//Abonnement
Route::middleware('auth:admin')->group(function(){
    Route::post('/ajout/abonnement', [AbonnementController::class, 'ajout_abonnement']);
    Route::post('/update/abonnement/{id}', [AbonnementController::class, 'update_abonnement']);
    Route::post('/delete/abonnement/{id}', [AbonnementController::class, 'delete_abonnement']);
});
//Liste des abonnements
Route::get('/abonnements', [AbonnementController::class, 'liste_abonnement']);