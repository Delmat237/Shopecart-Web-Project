<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 * schema="OrderItem",
 * title="OrderItem",
 * description="Modèle de données pour un article individuel dans une commande",
 * @OA\Property(property="id", type="integer", description="ID de l'article de commande"),
 * @OA\Property(property="order_id", type="integer", description="ID de la commande parente"),
 * @OA\Property(property="product_id", type="integer", description="ID du produit commandé"),
 * @OA\Property(property="product_name", type="string", description="Nom du produit (stocké au moment de la commande)"),
 * @OA\Property(property="product_sku", type="string", nullable=true, description="SKU du produit"),
 * @OA\Property(property="quantity", type="integer", description="Quantité commandée"),
 * @OA\Property(property="unit_price", type="number", format="float", description="Prix unitaire au moment de la commande"),
 * @OA\Property(property="total", type="number", format="float", description="Prix total de cet article (quantity * unit_price)"),
 * @OA\Property(property="options", type="object", description="Options du produit (taille, couleur, etc.)"),
 * @OA\Property(property="created_at", type="string", format="date-time"),
 * @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'unit_price', 'total',
        'product_name', 'product_sku', 'options'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'options' => 'array',
    ];

    // Relations
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}