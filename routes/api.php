<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\StripeController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'v1'], function () {
    //public routes
    Route::get('products', [ProductController::class, 'index']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('register', [UserController::class, 'register']);
    
    //protected route
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('loginWithToken', [UserController::class, 'loginWithToken']);
        Route::post('logout', [UserController::class, 'logout']);
        Route::patch('users/{id}', [UserController::class, 'update']);
        Route::post('card', [StripeController::class, 'addCard']);
        Route::post('checkout', [StripeController::class, 'checkout']);
        Route::get('orders/{id}',
            [OrderController::class, 'getProductIDQuantityList']
        );
        Route::get('orders', [OrderController::class, 'index']);
        
    });

    Route::get('invalid_token', function() {
        return response(['message' => 'token not exist'], 400);
    });
});

