<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $error;

    public function __construct($order, $error)
    {
        $this->order = $order;
        $this->error = $error;
    }

    public function build()
    {
        return $this->subject('Paiement échoué - Commande #' . $this->order->id)
                    ->markdown('emails.payments.failed');
    }
}