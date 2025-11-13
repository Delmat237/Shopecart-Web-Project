<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The order instance associated with the event.
     */
    public Order $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Diffusion sur un canal privé unique pour chaque commande
        return [
            new PrivateChannel('orders.' . $this->order->id),
        ];
    }

    /**
     * Optionnel : nom personnalisé de l’événement lors de la diffusion
     */
    public function broadcastAs(): string
    {
        return 'order.created';
    }

    /**
     * Optionnel : données à envoyer avec l’événement broadcasté
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'status' => $this->order->status,
            'total' => $this->order->total,
            'user_id' => $this->order->user_id,
            'created_at' => $this->order->created_at,
        ];
    }
}
