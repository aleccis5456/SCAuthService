<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/users', [AuthController::class, 'index']);
Route::post('/registerAdmin', [AuthController::class, 'registerAdmin']);
Route::post('/loginAdmin', [AuthController::class, 'loginAdmin']);
Route::get('/debug', [AuthController::class, 'whoIsLoged']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function(){    
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/validarToken', [AuthController::class, 'validarToken']);
});
