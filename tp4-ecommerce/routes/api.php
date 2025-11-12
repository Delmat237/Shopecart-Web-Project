<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

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
use App\Http\Controllers\NotificationController;

// Route de test (racine)
Route::get('/', function () {
    return response()->json([
        'message' => 'Shopcart API v1',
        'version' => '1.0.0',
        'endpoints' => [
            'register' => '/api/v1/register',
            'login' => '/api/v1/login',
            'docs' => '/api/documentation'
        ]
    ]);
});

Route::prefix('v1')->group(function () {

    // Test email
    Route::post('/notifications/test', [NotificationController::class, 'testEmail']);

    // Envoyé après application d'un code promo
    Route::post('/notifications/discount/{order}/{code}', [NotificationController::class, 'sendDiscountApplied']);
});

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});