<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Http\Resources\CartResource;
use Illuminate\Http\Request;
use App\Models\CartItem;
class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //function to emty a cart
    public function emptyCart($userId){
    $cart=Cart::where("userId",$userId)->get();
    $cartItems=CartItem::where("userId",$cart->id)->get();
    foreach($cartItems as $cartItem){
        $cartItem->delete();
        
    }
    return response()->json("cart emptied");
    }
    
    public function index()
    {
        //get all carts
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
        //create a new cart for the users
        $data=$request->validate([
            "userId"=>"required",
            
        ]);
        return response()->json(["data received"=>$data]);
        
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
        //
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
    public function removeItem(CartItem $cartItem)
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
    public function clear(Request $request)
    {
        // ... votre code existant
    }

    // ... vos méthodes privées existantes
}
