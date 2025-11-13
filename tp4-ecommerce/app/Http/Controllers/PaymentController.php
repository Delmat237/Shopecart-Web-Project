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
            
            return "hi";

      
    }


    public function storePayment(Request $request)
    {
    
    }

   
}
