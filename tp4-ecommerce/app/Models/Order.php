<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 * schema="Order",
 * title="Order",
 * description="Modèle de données pour une commande client",
 * @OA\Property(property="id", type="integer", description="ID de la commande"),
 * @OA\Property(property="order_number", type="string", description="Numéro unique de la commande"),
 * @OA\Property(property="user_id", type="integer", nullable=true, description="ID de l'utilisateur qui a passé la commande (null si invité)"),
 * @OA\Property(property="status", type="string", description="Statut de la commande", enum={"pending", "processing", "completed", "cancelled"}),
 * @OA\Property(property="customer_email", type="string", format="email", description="Email du client"),
 * @OA\Property(property="subtotal", type="number", format="float", description="Sous-total des articles"),
 * @OA\Property(property="shipping", type="number", format="float", description="Coût de la livraison"),
 * @OA\Property(property="tax", type="number", format="float", description="Taxes appliquées"),
 * @OA\Property(property="discount", type="number", format="float", description="Réduction appliquée"),
 * @OA\Property(property="total", type="number", format="float", description="Montant total de la commande"),
 * @OA\Property(property="payment_method", type="string", description="Méthode de paiement (ex: credit_card)"),
 * @OA\Property(property="payment_status", type="string", description="Statut du paiement (ex: paid, failed)"),
 * @OA\Property(property="shipping_address", type="string", description="Adresse de livraison complète"),
 * @OA\Property(property="shipping_city", type="string", description="Ville de livraison"),
 * @OA\Property(property="shipping_country", type="string", description="Pays de livraison"),
 * * @OA\Property(
 * property="items",
 * type="array",
 * description="Liste des articles de la commande",
 * @OA\Items(ref="#/components/schemas/OrderItem") 
 * ),
 * @OA\Property(property="created_at", type="string", format="date-time"),
 * @OA\Property(property="completed_at", type="string", format="date-time", nullable=true, description="Date de finalisation de la commande")
 * )
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'status', 'subtotal', 'shipping', 'tax', 'discount', 'total',
        'user_id', 'customer_email', 'customer_first_name', 'customer_last_name', 'customer_phone',
        'shipping_address', 'shipping_city', 'shipping_zipcode', 'shipping_country',
        'billing_address', 'billing_city', 'billing_zipcode', 'billing_country',
        'payment_method', 'payment_status', 'transaction_id', 'shipping_method', 'notes',
        'processed_at', 'completed_at', 'cancelled_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Méthodes métier
    public function markAsProcessing(): void
    {
        $this->status = 'processing';
        $this->processed_at = now();
        $this->save();
    }

    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    public function markAsCancelled(): void
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->save();
    }

    // Accessors
    public function getCustomerFullNameAttribute(): string
    {
        return $this->customer_first_name . ' ' . $this->customer_last_name;
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2, ',', ' ') . ' €';
    }
}