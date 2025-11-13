<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ce fichier contient toutes les routes API versionnées (v1).
| Les routes sont regroupées par module :
|   - Authentification
|   - Notifications
|   - Commandes (Orders)
|
*/

Route::prefix('v1')->group(function () {
    
    /**
     * -------------------------------
     * 🔹 Routes Publiques
     * -------------------------------
     */
    
    // Authentification
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Test notifications (peut rester public pour les tests)
    Route::post('/notifications/test', [NotificationController::class, 'testEmail']);
    
    /**
     * -------------------------------
     * 🔹 Routes Protégées (auth:sanctum)
     * -------------------------------
     */
    Route::middleware('auth:sanctum')->group(function () {
        
        // Authentification
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        
        // Notifications
        Route::post('/notifications/discount/{order}/{code}', [NotificationController::class, 'sendDiscountApplied']);
        
        /**
         * -------------------------------
         * 🔹 Routes Commandes (Orders)
         * -------------------------------
         */
        Route::prefix('orders')->group(function () {
            Route::post('/', [OrderController::class, 'store']);   // Créer commande (checkout)
            Route::get('/', [OrderController::class, 'index']);    // Liste des commandes
            Route::get('/{id}', [OrderController::class, 'show']); // Détails commande
        });
        
        /**
         * -------------------------------
         * 🔹 Routes Admin uniquement
         * -------------------------------
         */
        Route::prefix('admin')->middleware('role:admin')->group(function () {
            // Gestion des commandes
            Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
        });
    });
});