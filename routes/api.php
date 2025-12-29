<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\RiderController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

Route::post('/login/user', [AuthController::class, 'userLogin']);
Route::post('/login/rider', [AuthController::class, 'riderLogin']);
Route::post('/register/user', [AuthController::class, 'userRegister']);
Route::post('/register/rider', [AuthController::class, 'riderRegister']);



/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/items', [ItemController::class, 'index']);

/*
|--------------------------------------------------------------------------
| User (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add-item', [CartController::class, 'addItem']);
    Route::put('/cart/update-item/{id}', [CartController::class, 'updateItem']);
    Route::delete('/cart/remove-item/{id}', [CartController::class, 'removeItem']);
    Route::post('/cart/confirm', [CartController::class, 'confirm']);

    Route::get('/transactions', [TransactionController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| Rider
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:rider'])->prefix('rider')->group(function () {

    Route::get('/orders', [RiderController::class, 'orders']);

    Route::post('/order/{order_id}/accept', [RiderController::class, 'acceptOrder']);

    Route::get('/order/{order_id}', [RiderController::class, 'orderDetails']);

    Route::post('/order/{order_id}/update-weight', [RiderController::class, 'updateWeight']);

    Route::post('/order/{order_id}/complete', [RiderController::class, 'completeOrder']);

    Route::get('/my-orders', [RiderController::class, 'myOrders']);

    Route::get('/history', [RiderController::class, 'history']);
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/orders', [AdminController::class, 'allOrders']);
    Route::get('/orders/status/{status}', [AdminController::class, 'ordersByStatus']);
    Route::get('/riders', [AdminController::class, 'allRiders']);
    Route::put('/order/{order_id}/status', [AdminController::class, 'updateOrderStatus']);
    Route::get('/analytics', [AdminController::class, 'analytics']);
});

Route::post('/items', [ItemController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    Route::put('/user/change-password', [UserController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
