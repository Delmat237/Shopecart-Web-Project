<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Display the user's cart.
     */
    public function show(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        $cart->load('items.product');

        return new CartResource($cart);
    }

    /**
     * Add item to cart.
     */
    public function addItem(Request $request, Product $product)
    {
        // Vérifier le stock
        if ($product->stock < 1) {
            return response()->json([
                'message' => 'Product out of stock'
            ], 422);
        }

        $cart = $this->getOrCreateCart($request);
        $quantity = $request->input('quantity', 1);

        // Vérifier si le produit est déjà dans le panier
        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            // Mettre à jour la quantité
            $newQuantity = $existingItem->quantity + $quantity;
            
            if ($newQuantity > $product->stock) {
                return response()->json([
                    'message' => 'Requested quantity not available in stock'
                ], 422);
            }
            
            $existingItem->update([
                'quantity' => $newQuantity,
                'total' => $existingItem->unit_price * $newQuantity
            ]);
        } else {
            // Vérifier le stock disponible
            if ($quantity > $product->stock) {
                return response()->json([
                    'message' => 'Requested quantity not available in stock'
                ], 422);
            }
            
            // Ajouter un nouvel item
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'total' => $product->price * $quantity,
            ]);
        }

        // Mettre à jour les totaux du panier
        $this->updateCartTotals($cart);
        $cart->load('items.product');

        return new CartResource($cart);
    }

    /**
     * Update cart item quantity.
     */
    public function updateItem(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Vérifier le stock disponible
        if ($request->quantity > $cartItem->product->stock) {
            return response()->json([
                'message' => 'Requested quantity not available in stock'
            ], 422);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
            'total' => $cartItem->unit_price * $request->quantity
        ]);

        $this->updateCartTotals($cartItem->cart);
        $cartItem->cart->load('items.product');

        return new CartResource($cartItem->cart);
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(CartItem $cartItem)
    {
        $cartItem->delete();
        $this->updateCartTotals($cartItem->cart);
        $cartItem->cart->load('items.product');

        return new CartResource($cartItem->cart);
    }

    /**
     * Clear the cart.
     */
    public function clear(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        $cart->items()->delete();
        $this->updateCartTotals($cart);

        return response()->json([
            'message' => 'Cart cleared successfully',
            'cart' => new CartResource($cart)
        ]);
    }

    /**
     * Get or create cart
     */
    private function getOrCreateCart(Request $request)
    {
        // Si l'utilisateur est connecté
        if (auth()->check()) {
            return Cart::firstOrCreate([
                'user_id' => auth()->id()
            ]);
        }

        // Pour les utilisateurs non connectés, utiliser la session ou token
        $sessionId = $request->header('X-Cart-Session') ?? $request->session()->get('cart_session_id');
        
        if (!$sessionId) {
            $sessionId = Str::random(32);
            $request->session()->put('cart_session_id', $sessionId);
        }

        return Cart::firstOrCreate([
            'session_id' => $sessionId
        ]);
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