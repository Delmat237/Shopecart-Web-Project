<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        $this->cart->updateTotals();
    }

    public function decrementQuantity(int $amount = 1): void
    {
        $this->quantity = max(0, $this->quantity - $amount);
        
        if ($this->quantity === 0) {
            $this->delete();
        } else {
            $this->updateTotal();
        }
        
        $this->cart->updateTotals();
    }
}