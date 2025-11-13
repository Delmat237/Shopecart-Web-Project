<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/orders",
     *     operationId="createOrder",
     *     tags={"Commandes"},
     *     summary="Créer une commande (Checkout)",
     *     description="Transforme le panier actuel en commande. Calcule automatiquement les totaux, applique les remises et décrémente le stock.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"shipping_address", "phone"},
     *             @OA\Property(property="discount_code", type="string", example="PROMO10", description="Code promo optionnel"),
     *             @OA\Property(property="shipping_address", type="string", example="123 Avenue de la Liberté, Yaoundé, Cameroun", description="Adresse de livraison complète"),
     *             @OA\Property(property="phone", type="string", example="+237 6 XX XX XX XX", description="Numéro de téléphone")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Commande créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Commande créée avec succès"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="order",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="order_number", type="string", example="ORD-000001"),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="subtotal", type="number", format="float", example=75000.00),
     *                     @OA\Property(property="discount_amount", type="number", format="float", example=7500.00),
     *                     @OA\Property(property="shipping_cost", type="number", format="float", example=0.00),
     *                     @OA\Property(property="total_amount", type="number", format="float", example=67500.00),
     *                     @OA\Property(
     *                         property="items",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="product_name", type="string", example="iPhone 15 Pro"),
     *                             @OA\Property(property="variant_name", type="string", example="256GB Noir"),
     *                             @OA\Property(property="quantity", type="integer", example=1),
     *                             @OA\Property(property="unit_price", type="number", format="float", example=750000.00),
     *                             @OA\Property(property="subtotal", type="number", format="float", example=750000.00)
     *                         )
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-12 15:30:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erreur lors de la création (panier vide, stock insuffisant, etc.)",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Erreur lors de la création de la commande"),
     *             @OA\Property(property="error", type="string", example="Stock insuffisant pour iPhone 15 Pro - Disponible: 2, Demandé: 5")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $cart = $user->cart()->with('items.variant.product')->first();

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre panier est vide'
                ], 400);
            }

            $order = $this->orderService->createFromCart(
                $cart,
                $request->discount_code,
                [
                    'address' => $request->shipping_address,
                    'phone' => $request->phone,
                ]
            );

            $cart->items()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'data' => [
                    'order' => [
                        'id' => $order->id,
                        'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                        'status' => $order->status,
                        'subtotal' => $order->subtotal,
                        'discount_amount' => $order->discount_amount,
                        'shipping_cost' => $order->shipping_cost,
                        'total_amount' => $order->total_amount,
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->variant->product->name,
                                'variant_name' => $item->variant->name ?? '',
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'subtotal' => $item->subtotal,
                            ];
                        }),
                        'created_at' => $order->created_at->toDateTimeString(),
                    ]
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la commande',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders",
     *     operationId="listOrders",
     *     tags={"Commandes"},
     *     summary="Liste des commandes de l'utilisateur",
     *     description="Récupère l'historique des commandes de l'utilisateur authentifié avec pagination",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Nombre de résultats par page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des commandes récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="orders",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="order_number", type="string", example="ORD-000001"),
     *                         @OA\Property(property="status", type="string", example="delivered"),
     *                         @OA\Property(property="total_amount", type="number", format="float", example=67500.00),
     *                         @OA\Property(property="items_count", type="integer", example=3),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-12 15:30:00")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer", example=1),
     *                     @OA\Property(property="last_page", type="integer", example=5),
     *                     @OA\Property(property="per_page", type="integer", example=15),
     *                     @OA\Property(property="total", type="integer", example=73)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $orders = $this->orderService->getUserOrders($request->user()->id, $perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                        'status' => $order->status,
                        'total_amount' => $order->total_amount,
                        'items_count' => $order->items->count(),
                        'created_at' => $order->created_at->toDateTimeString(),
                    ];
                }),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/orders/{id}",
     *     operationId="getOrderDetails",
     *     tags={"Commandes"},
     *     summary="Détails d'une commande",
     *     description="Récupère tous les détails d'une commande spécifique (articles, montants, informations de livraison)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la commande",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la commande récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="order",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="order_number", type="string", example="ORD-000001"),
     *                     @OA\Property(property="status", type="string", example="delivered"),
     *                     @OA\Property(property="subtotal", type="number", format="float", example=75000.00),
     *                     @OA\Property(property="discount_amount", type="number", format="float", example=7500.00),
     *                     @OA\Property(property="discount_code", type="string", example="PROMO10", nullable=true),
     *                     @OA\Property(property="shipping_cost", type="number", format="float", example=0.00),
     *                     @OA\Property(property="total_amount", type="number", format="float", example=67500.00),
     *                     @OA\Property(property="shipping_address", type="string", example="123 Avenue de la Liberté, Yaoundé"),
     *                     @OA\Property(property="phone", type="string", example="+237 6 XX XX XX XX"),
     *                     @OA\Property(
     *                         property="items",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="product_name", type="string", example="iPhone 15 Pro"),
     *                             @OA\Property(property="variant_name", type="string", example="256GB Noir"),
     *                             @OA\Property(property="quantity", type="integer", example=1),
     *                             @OA\Property(property="unit_price", type="number", format="float", example=750000.00),
     *                             @OA\Property(property="subtotal", type="number", format="float", example=750000.00),
     *                             @OA\Property(property="image", type="string", example="http://localhost:8000/storage/products/iphone15.jpg", nullable=true)
     *                         )
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-11-12 15:30:00"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-12 15:30:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Commande introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Commande introuvable")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderDetails($id, $request->user()->id);

            return response()->json([
                'success' => true,
                'data' => [
                    'order' => [
                        'id' => $order->id,
                        'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                        'status' => $order->status,
                        'subtotal' => $order->subtotal,
                        'discount_amount' => $order->discount_amount,
                        'discount_code' => $order->discount_code,
                        'shipping_cost' => $order->shipping_cost,
                        'total_amount' => $order->total_amount,
                        'shipping_address' => $order->shipping_address,
                        'phone' => $order->phone,
                        'items' => $order->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'product_name' => $item->variant->product->name,
                                'variant_name' => $item->variant->name ?? '',
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                                'subtotal' => $item->subtotal,
                                'image' => $item->variant->image 
                                    ? url('storage/' . $item->variant->image) 
                                    : null,
                            ];
                        }),
                        'created_at' => $order->created_at->toDateTimeString(),
                        'updated_at' => $order->updated_at->toDateTimeString(),
                    ]
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Commande introuvable'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/admin/orders/{id}/status",
     *     operationId="updateOrderStatus",
     *     tags={"Admin"},
     *     summary="Mettre à jour le statut d'une commande (Admin uniquement)",
     *     description="Permet à un administrateur de changer le statut d'une commande (ex: de 'paid' à 'preparing')",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la commande",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"pending", "paid", "preparing", "shipped", "delivered", "canceled"},
     *                 example="preparing",
     *                 description="Nouveau statut de la commande"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statut mis à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Statut de la commande mis à jour"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="order",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="order_number", type="string", example="ORD-000001"),
     *                     @OA\Property(property="status", type="string", example="preparing"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-12 16:00:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Transition de statut invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Transition de statut invalide: delivered → preparing")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé (pas administrateur)",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Accès non autorisé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Commande introuvable",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Commande introuvable")
     *         )
     *     )
     * )
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        try {
            $order = Order::findOrFail($id);
            
            $updatedOrder = $this->orderService->updateStatus($order, $request->status);

            return response()->json([
                'success' => true,
                'message' => 'Statut de la commande mis à jour',
                'data' => [
                    'order' => [
                        'id' => $updatedOrder->id,
                        'order_number' => 'ORD-' . str_pad($updatedOrder->id, 6, '0', STR_PAD_LEFT),
                        'status' => $updatedOrder->status,
                        'updated_at' => $updatedOrder->updated_at->toDateTimeString(),
                    ]
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}