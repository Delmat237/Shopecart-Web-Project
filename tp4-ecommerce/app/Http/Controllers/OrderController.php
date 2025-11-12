<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return new OrderCollection($orders);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $cart = $this->getCurrentCart($request);

        // Vérifications finales
        if ($cart->items_count === 0) {
            return response()->json([
                'message' => 'Your cart is empty'
            ], 422);
        }

        // Valider les informations
        $validated = $request->validate([
            'customer_first_name' => 'required|string|max:255',
            'customer_last_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_zipcode' => 'required|string',
            'shipping_country' => 'required|string',
            'billing_address' => 'nullable|string',
            'billing_city' => 'nullable|string',
            'billing_zipcode' => 'nullable|string',
            'billing_country' => 'nullable|string',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Vérifier le stock de tous les produits
        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->stock) {
                return response()->json([
                    'message' => "Product {$item->product->name} does not have enough stock"
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Créer la commande
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . Str::random(6),
                'status' => 'pending',
                'subtotal' => $cart->total,
                'shipping' => 0, // À calculer
                'tax' => 0, // À calculer
                'total' => $cart->total,
                'user_id' => auth()->id(),
                'customer_email' => $validated['customer_email'],
                'customer_first_name' => $validated['customer_first_name'],
                'customer_last_name' => $validated['customer_last_name'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_zipcode' => $validated['shipping_zipcode'],
                'shipping_country' => $validated['shipping_country'],
                'billing_address' => $validated['billing_address'] ?? $validated['shipping_address'],
                'billing_city' => $validated['billing_city'] ?? $validated['shipping_city'],
                'billing_zipcode' => $validated['billing_zipcode'] ?? $validated['shipping_zipcode'],
                'billing_country' => $validated['billing_country'] ?? $validated['shipping_country'],
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Créer les items de commande
            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total' => $cartItem->total,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                ]);

                // Mettre à jour le stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Vider le panier
            $cart->items()->delete();
            $this->updateCartTotals($cart);

            DB::commit();

            $order->load('items.product');

            return response()->json([
                'message' => 'Order created successfully',
                'order' => new OrderResource($order)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Error creating order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Vérifier que l'utilisateur peut voir cette commande
        if ($order->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->load('items.product');

        return new OrderResource($order);
    }

    /**
     * Get current cart
     */
    private function getCurrentCart(Request $request)
    {
        if (auth()->check()) {
            return Cart::where('user_id', auth()->id())->first();
        }

        $sessionId = $request->header('X-Cart-Session') ?? $request->session()->get('cart_session_id');
        return Cart::where('session_id', $sessionId)->first();
    }

    /**
     * Update cart totals
     */
    private function updateCartTotals(Cart $cart)
    {
        $cart->load('items');
        
        $cart->update([
            'items_count' => $cart->items->sum('quantity'),
            'total' => $cart->items->sum('total')
        ]);
    }
}