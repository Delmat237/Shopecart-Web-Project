<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 * schema="CartItem",
 * title="CartItem",
 * description="Modèle de données pour un article dans le panier",
 * @OA\Property(
 * property="id",
 * type="integer",
 * description="ID de l'article dans le panier"
 * ),
 * @OA\Property(
 * property="cart_id",
 * type="integer",
 * description="ID du panier auquel l'article appartient"
 * ),
 * @OA\Property(
 * property="product_id",
 * type="integer",
 * description="ID du produit dans cet article"
 * ),
 * @OA\Property(
 * property="quantity",
 * type="integer",
 * description="Quantité du produit"
 * ),
 * @OA\Property(
 * property="product",
 * description="Détails du produit associé",
 * ref="#/components/schemas/Product" 
 * ),
 * @OA\Property(
 * property="created_at",
 * type="string",
 * format="date-time"
 * ),
 * @OA\Property(
 * property="updated_at",
 * type="string",
 * format="date-time"
 * )
 * )
 */
class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id', 'product_id', 'quantity', 'unit_price', 'total', 'options'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'options' => 'array',
    ];

    // Relations
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Méthodes métier
    public function updateTotal(): void
    {
        $this->total = $this->quantity * $this->unit_price;
        $this->save();
    }

    public function incrementQuantity(int $amount = 1): void
    {
        $this->quantity += $amount;
        $this->updateTotal();
        // Assurez-vous que $this->cart existe avant d'appeler updateTotals()
        if (isset($this->cart)) {
            $this->cart->updateTotals();
        }
    }

    public function decrementQuantity(int $amount = 1): void
    {
        $this->quantity = max(0, $this->quantity - $amount);
        
        if ($this->quantity === 0) {
            $this->delete();
        } else {
            $this->updateTotal();
        }
        
        // Assurez-vous que $this->cart existe avant d'appeler updateTotals()
        if (isset($this->cart)) {
            $this->cart->updateTotals();
        }
    }
}