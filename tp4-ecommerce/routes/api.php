<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\PaymentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use App\Http\Controllers\Api\NotificationController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

Route::prefix("cart")->group(function(){
    Route::post("/abs",[CartController::class,"store"]);
    Route::get("/user/{userId}/empty",[CartController::class,"emptyCart"]);
});

Route::prefix("cartItems")->group(function(){
    //get cart items
    //add cart item
    //remove cart item
    //update cart item
    Route::get("/cart/{cartId}",[CartItemController::class,"getAllCartItems"]);
    Route::get("/{cartItemId}",[CartItemController::class,"getCartItem"]);
    Route::post("/",[CartItemController::class,"addCartItem"]);
    Route::put("/{cartItemId}",[CartItemController::class,"updateCartItem"]);
     Route::delete("/{cartItemId}",[CartItemController::class,"deleteCartItem"]);

});
Route::prefix("payment")->group(
    function(){
        Route::post("/create-payment-intent/order/{orderId}",[PaymentController::class,"createPaymentIntentWithCardd"]);
        Route::post("/store",[PaymentController::class,"storePayment"]);

    }
);
