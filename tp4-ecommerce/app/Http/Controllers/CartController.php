<?php

namespace App\Http\Controllers;

use App\Models\Cart;
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //create a new cart for the users
        $data=$request->validate([
            "userId"=>"required",
            
        ]);
        return response()->json(["data received"=>$data]);
        
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

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
