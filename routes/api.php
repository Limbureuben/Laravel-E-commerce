<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PaymentController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/users', [UsersController::class, 'index']);
Route::delete('/users/{id}', [UsersController::class, 'destroy']);
// Route::middleware('auth:sanctum')->delete('/users/{id}', [UsersController::class, 'destroy']);
Route::post('/add-products', [ProductController::class, 'store']);
Route::get('/products', [ProductController::class, 'index']);
Route::put('/products/{id}', [ProductController::class, 'update']);
// Route::middleware('auth:sanctum')->group(function () {
//     Route::put('/products/{id}', [ProductController::class, 'update']);
// });

Route::delete('/products/{id}', [ProductController::class, 'destroy']);
// Route::middleware('auth:sanctum')->group(function () {
//     Route::delete('/products/{id}', [ProductController::class, 'destroy']);
// });

Route::get('/products/landing', [ProductController::class, 'landing']);
Route::get('/user-product', [ProductController::class, 'userproduct']);

// Route::middleware('auth:sanctum')->post('/rating', [RatingController::class, 'rate']);
Route::post('/rating', [ProductController::class, 'rate']);

Route::get('/payment/token', [PaymentController::class, 'testToken']);
Route::get('/ipn-handler', [PaymentController::class, 'callback']);
Route::post('/register-ipn', [PaymentController::class, 'registerIPN']);
Route::post('/submit-payment', [PaymentController::class, 'submitOrder']);

// Route::get('/test-token', [PaymentController::class, 'testToken']);


