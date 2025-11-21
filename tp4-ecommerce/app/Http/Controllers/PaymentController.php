<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Enums\PaymentStatus;


class PaymentController extends Controller

{
    /**
     * Display a listing of the resource.
     */

    public function createPaymentIntentWithCardd(Request $request,$orderId){

        $data=$request->validate([
            "amount"=>"required",
            "paymentMethod"=>"required"
            
        ]);
        /*

        
        */
        $payment=Payment::create([
            "amount"=>$data["amount"],
            "paymentMethod"=>$data["paymentMethod"],
            "transactionId"=>null
        ]);


        Stripe::setApiKey(env.)

        $paymentIntent = PaymentIntent::create([
        "amount" => $payment->amount,
        "currency" => "usd", // Stripe does NOT support XAF (important)
        "metadata" => [
            
            "payment_id" => $payment->id
        ],
    
    ]);

    // 4. Return client secret to the frontend
    return response()->json([
        "clientSecret" => $paymentIntent->client_secret,
        "payment" => $payment
    ]);
            
            

      
    }

    


    public function storePayment(Request $request)
    {
    
    }

   
}
