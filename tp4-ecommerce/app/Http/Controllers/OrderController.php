<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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
     * POST /api/v1/orders [checkout]
     * Créer une commande depuis le panier
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Récupérer le panier de l'utilisateur
            $cart = $user->cart()->with('items.variant.product')->first();

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre panier est vide'
                ], 400);
            }

            // Créer la commande
            $order = $this->orderService->createFromCart(
                $cart,
                $request->discount_code,
                [
                    'address' => $request->shipping_address,
                    'phone' => $request->phone,
                ]
            );

            // Vider le panier après création de la commande
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
     * GET /api/v1/orders
     * Liste des commandes de l'utilisateur connecté
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
     * GET /api/v1/orders/{id}
     * Détails d'une commande spécifique
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
     * PUT /api/v1/admin/orders/{id}/status [admin]
     * Mettre à jour le statut d'une commande (Admin uniquement)
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