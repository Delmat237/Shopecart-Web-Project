<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Listener 1: Envoi de l'email de confirmation
 */
class SendOrderConfirmationEmail
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        
        // A gérera l'envoi réel du mail (OrderConfirmed Mailable)
        // Pour l'instant on log
        Log::info('Order confirmation email should be sent', [
            'order_id' => $order->id,
            'user_email' => $order->user->email,
            'total' => $order->total_amount,
        ]);

        // Exemple d'utilisation future par A:
        // Mail::to($order->user->email)->send(new OrderConfirmed($order));
    }
}