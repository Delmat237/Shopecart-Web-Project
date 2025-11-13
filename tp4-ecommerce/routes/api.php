<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\DiscountCodesController;
use App\Http\Controllers\DiscountCodeUsageController;

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
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    // Test email
    Route::post('/notifications/test', [NotificationController::class, 'testEmail']);

    // Envoyé après application d'un code promo
    Route::post('/notifications/discount/{order}/{code}', [NotificationController::class, 'sendDiscountApplied']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
    });
});

// ============================================
// ROUTES DE PRODUCTION - DISCOUNTS
// ============================================

Route::prefix('v1')->group(function () {
    
    // Routes pour les remises (ADMIN)
    Route::apiResource('discounts', DiscountController::class);
    Route::post('discounts/{discount}/toggle', [DiscountController::class, 'toggle']);
    Route::get('products/{product}/discounts', [DiscountController::class, 'forProduct']);

    // Routes pour les codes promo (ADMIN)
    Route::apiResource('discount-codes', DiscountCodesController::class);
    Route::post('discount-codes/{discountCode}/toggle', [DiscountCodesController::class, 'toggle']);
    Route::get('discount-codes/generate/new', [DiscountCodesController::class, 'generate']);
    
    // Route publique pour valider un code promo (FRONTEND)
    Route::post('discount-codes/validate', [DiscountCodesController::class, 'validate']);

    // Routes pour l'historique d'utilisation (ADMIN)
    Route::prefix('discount-code-usages')->group(function () {
        Route::get('/', [DiscountCodeUsageController::class, 'index']);
        Route::get('/{discountCodeUsage}', [DiscountCodeUsageController::class, 'show']);
        Route::get('/statistics/all', [DiscountCodeUsageController::class, 'statistics']);
        Route::get('/user/history', [DiscountCodeUsageController::class, 'userHistory']);
        Route::post('/check-usage', [DiscountCodeUsageController::class, 'checkUserUsage']);
        Route::get('/export/csv', [DiscountCodeUsageController::class, 'export']);
    });
});

// ============================================
// ROUTES DE TEST - À RETIRER EN PRODUCTION
// ============================================

Route::prefix('test')->group(function () {
    
    // TEST 1 : Voir toutes les remises
    Route::get('/discounts/all', function () {
        $discounts = \App\Models\Discount::with('discountCodes')->get();
        return response()->json([
            'total' => $discounts->count(),
            'discounts' => $discounts
        ]);
    });

    // TEST 2 : Voir les remises actives uniquement
    Route::get('/discounts/active', function () {
        $discounts = \App\Models\Discount::active()->with('discountCodes')->get();
        return response()->json([
            'total' => $discounts->count(),
            'discounts' => $discounts
        ]);
    });

    // TEST 3 : Tester un produit avec remises
    Route::get('/product/{id}/with-discount', function ($id, \App\Services\DiscountService $discountService) {
        $product = \App\Models\Product::findOrFail($id);
        $priceInfo = $discountService->getBestPriceForProduct($product);
        
        return response()->json([
            'product' => $product,
            'pricing' => $priceInfo
        ]);
    });

    // TEST 4 : Valider un code promo
    Route::post('/validate-code', function (\Illuminate\Http\Request $request, \App\Services\DiscountService $discountService) {
        $validation = $discountService->validateDiscountCode(
            $request->code,
            null,
            $request->cart_total ?? 100
        );
        
        return response()->json($validation);
    });

    // TEST 5 : Simuler un panier avec code promo
    Route::post('/cart/calculate', function (\Illuminate\Http\Request $request, \App\Services\DiscountService $discountService) {
        $products = \App\Models\Product::whereIn('id', $request->product_ids ?? [1, 2, 3])->get();
        
        $cartItems = $products->map(function ($product) {
            return (object) [
                'product' => $product,
                'quantity' => 1
            ];
        });
        
        $result = $discountService->calculateCartTotal(
            $cartItems,
            $request->discount_code ?? null,
            null
        );
        
        return response()->json($result);
    });

    // TEST 6 : Statistiques
    Route::get('/discounts/stats', function () {
        return response()->json([
            'total_discounts' => \App\Models\Discount::count(),
            'active_discounts' => \App\Models\Discount::active()->count(),
            'total_codes' => \App\Models\DiscountCodes::count(),
            'active_codes' => \App\Models\DiscountCodes::active()->count(),
            'expired_discounts' => \App\Models\Discount::where('end_date', '<', now())->count(),
        ]);
    });
});