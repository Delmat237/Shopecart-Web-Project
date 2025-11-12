<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Cart",
 *     description="API Endpoints for Shopping Cart"
 * )
 */
class CartController extends Controller
{
    /**
     * @OA\Get(
     *     path="/cart",
     *     summary="Get user's cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     )
     * )
     */
    public function show(Request $request)
    {
        $cart = $this->getOrCreateCart($request);
        $cart->load('items.product');

        return new CartResource($cart);
    }

    /**
     * @OA\Post(
     *     path="/cart/add/{product}",
     *     summary="Add item to cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={},
     *             @OA\Property(property="quantity", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item added to cart",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function addItem(Request $request, Product $product)
    {
        // ... votre code existant
    }

    /**
     * @OA\Put(
     *     path="/cart/items/{cartItem}",
     *     summary="Update cart item quantity",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="cartItem",
     *         in="path",
     *         required=true,
     *         description="Cart Item ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity"},
     *             @OA\Property(property="quantity", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart item updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function updateItem(Request $request, CartItem $cartItem)
    {
        // ... votre code existant
    }

    /**
     * @OA\Delete(
     *     path="/cart/items/{cartItem}",
     *     summary="Remove item from cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="cartItem",
     *         in="path",
     *         required=true,
     *         description="Cart Item ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Item removed from cart",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     )
     * )
     */
<<<<<<< HEAD
    public function show(Cart $cart)
=======
    public function removeItem(CartItem $cartItem)
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
    {
        // ... votre code existant
    }

    /**
     * @OA\Delete(
     *     path="/cart/clear",
     *     summary="Clear the cart",
     *     tags={"Cart"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cart cleared successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     )
     * )
     */
<<<<<<< HEAD
    public function edit(Cart $cart)
=======
    public function clear(Request $request)
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
    {
        // ... votre code existant
    }

<<<<<<< HEAD
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
=======
    // ... vos méthodes privées existantes
}
>>>>>>> e522c3c00ac8b71bb74283329c57d127c6d0411c
