<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id', 'user_id', 'total', 'items_count'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    // Relation utilisateur
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relation éléments du panier
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Méthodes métier
    public function updateTotals(): void
    {
        $this->items_count = $this->items->sum('quantity');
        $this->total = $this->items->sum('total');
        $this->save();
    }

    public function isEmpty(): bool
    {
        return $this->items_count === 0;
    }

    public function clear(): void
    {
        $this->items()->delete();
        $this->updateTotals();
    }
}