<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'compare_price', 'stock',
        'sku', 'barcode', 'is_visible', 'is_featured', 'published_at',
        'meta_title', 'meta_description', 'image', 'gallery', 'category_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'gallery' => 'array',
    ];

    // Relation avec Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relation avec CartItem
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Relation avec OrderItem
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true)
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2, ',', ' ') . ' €';
    }

    public function getHasDiscountAttribute(): bool
    {
        return !is_null($this->compare_price) && $this->compare_price > $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->has_discount) return null;
        
        return round(($this->compare_price - $this->price) / $this->compare_price * 100);
    }
}