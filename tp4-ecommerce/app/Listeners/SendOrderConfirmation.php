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

/**
 * Listener 2: Notification admin
 */
class NotifyAdminNewOrder
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        
        Log::info('New order notification for admin', [
            'order_id' => $order->id,
            'user' => $order->user->name,
            'total' => $order->total_amount,
            'items_count' => $order->items->count(),
        ]);

        // A pourra ajouter une notification admin ici
    }
}

/**
 * Listener 3: Mise à jour des statistiques
 */
class UpdateOrderStatistics
{
    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;
        
        // Logique pour B (Dashboard Admin)
        Log::info('Order statistics updated', [
            'order_id' => $order->id,
            'date' => $order->created_at->toDateString(),
            'amount' => $order->total_amount,
        ]);

        // B pourra implémenter le cache des stats ici
    }
}