<?php

namespace App\Services;

use App\Events\OrderCreated;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    protected DiscountService $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * Constantes
     */
    const FREE_SHIPPING_THRESHOLD = 50000; // Gratuit si > 50 000 FCFA
    const STANDARD_SHIPPING_COST = 2000; // 2000 FCFA

    /**
     * Créer une commande depuis le panier
     */
    public function createFromCart(
        Cart $cart,
        ?string $discountCode = null,
        ?array $shippingData = null
    ): Order {
        DB::beginTransaction();

        try {
            // 1. Vérifier que le panier n'est pas vide
            if ($cart->items->isEmpty()) {
                throw new Exception('Le panier est vide');
            }

            // 2. Vérifier la disponibilité du stock
            $this->validateStock($cart);

            // 3. Calculer le sous-total
            $subtotal = $this->calculateSubtotal($cart);

            // 4. Appliquer les remises (via F)
            $discountAmount = 0;
            if ($discountCode) {
                $discountAmount = $this->discountService->apply($cart, $discountCode);
            }

            // 5. Calculer les frais de port
            $shippingCost = $this->calculateShippingCost($subtotal - $discountAmount);

            // 6. Calculer le total
            $totalAmount = $subtotal - $discountAmount + $shippingCost;

            // 7. Créer la commande
            $order = Order::create([
                'user_id' => $cart->user_id,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_code' => $discountCode,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'status' => Order::STATUS_PENDING,
                'shipping_address' => $shippingData['address'] ?? null,
                'phone' => $shippingData['phone'] ?? null,
            ]);

            // 8. Créer les items de commande
            foreach ($cart->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->variant->price,
                ]);
            }

            // 9. Décrémenter le stock
            $this->decrementStock($cart);

            // 10. Déclencher l'événement OrderCreated
            event(new OrderCreated($order->load(['items.variant', 'user'])));

            DB::commit();

            return $order->load(['items.variant']);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calculer le sous-total du panier
     */
    protected function calculateSubtotal(Cart $cart): float
    {
        $subtotal = 0;

        foreach ($cart->items as $item) {
            $subtotal += $item->variant->price * $item->quantity;
        }

        return $subtotal;
    }

    /**
     * Calculer les frais de port
     */
    protected function calculateShippingCost(float $amount): float
    {
        // Gratuit si montant > seuil
        if ($amount >= self::FREE_SHIPPING_THRESHOLD) {
            return 0;
        }

        return self::STANDARD_SHIPPING_COST;
    }

    /**
     * Valider la disponibilité du stock
     */
    protected function validateStock(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            $variant = $item->variant;

            if ($variant->stock < $item->quantity) {
                throw new Exception(
                    "Stock insuffisant pour {$variant->product->name} - " .
                    "Disponible: {$variant->stock}, Demandé: {$item->quantity}"
                );
            }
        }
    }

    /**
     * Décrémenter le stock des produits
     */
    protected function decrementStock(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            $variant = ProductVariant::find($item->product_variant_id);
            $variant->decrement('stock', $item->quantity);
        }
    }

    /**
     * Mettre à jour le statut d'une commande (Admin)
     */
    public function updateStatus(Order $order, string $newStatus): Order
    {
        if (!$order->canUpdateStatus($newStatus)) {
            throw new Exception(
                "Transition de statut invalide: {$order->status} → {$newStatus}"
            );
        }

        $order->update(['status' => $newStatus]);

        // Log pour traçabilité
        activity()
            ->performedOn($order)
            ->withProperties(['old_status' => $order->getOriginal('status'), 'new_status' => $newStatus])
            ->log('Order status updated');

        return $order->fresh();
    }

    /**
     * Obtenir l'historique des commandes d'un utilisateur
     */
    public function getUserOrders(int $userId, int $perPage = 15)
    {
        return Order::forUser($userId)
            ->recent()
            ->with(['items.variant.product'])
            ->paginate($perPage);
    }

    /**
     * Obtenir les détails d'une commande
     */
    public function getOrderDetails(int $orderId, ?int $userId = null): Order
    {
        $query = Order::with(['items.variant.product', 'user']);

        if ($userId) {
            $query->forUser($userId);
        }

        $order = $query->findOrFail($orderId);

        return $order;
    }
}