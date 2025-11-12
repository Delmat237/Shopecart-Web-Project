<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'color',
        'size',
        'price',
        'stock',
        'sku'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('stock', '>', 0)
                     ->where('stock', '<=', $threshold);
    }

    public function decrementStock(int $quantity)
    {
        if ($this->stock < $quantity) {
            throw new \Exception("Stock insuffisant pour la variante {$this->name}");
        }
        $this->decrement('stock', $quantity);
    }

    public function incrementStock(int $quantity)
    {
        $this->increment('stock', $quantity);
    }
}