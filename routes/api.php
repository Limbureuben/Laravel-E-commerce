<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsersController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users', [UsersController::class, 'index']);
Route::delete('/users/{id}', [UsersController::class, 'destroy']);
// Route::middleware('auth:sanctum')->delete('/users/{id}', [UsersController::class, 'destroy']);
