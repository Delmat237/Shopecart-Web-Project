<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'value',
        'start_date',
        'end_date',
        'is_active',
        'priority',
        'apply_to_all_products',
        'min_purchase_amount',
        'max_usage',
        'current_usage',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'apply_to_all_products' => 'boolean',
        'min_purchase_amount' => 'decimal:2',
        'priority' => 'integer',
        'max_usage' => 'integer',
        'current_usage' => 'integer',
    ];

    // Relations
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'discount_product')
                    ->withTimestamps();
    }

    public function discountCodes(): HasMany
    {
        return $this->hasMany(DiscountCodes::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeForProduct($query, $productId)
    {
        return $query->where(function($q) use ($productId) {
            $q->where('apply_to_all_products', true)
              ->orWhereHas('products', function($q) use ($productId) {
                  $q->where('products.id', $productId);
              });
        });
    }

    public function scopeAvailable($query)
    {
        return $query->where(function($q) {
            $q->whereNull('max_usage')
              ->orWhereColumn('current_usage', '<', 'max_usage');
        });
    }

    // Méthodes métier
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->start_date > $now || $this->end_date < $now) {
            return false;
        }

        if ($this->max_usage && $this->current_usage >= $this->max_usage) {
            return false;
        }

        return true;
    }

    public function canApplyToProduct(Product $product): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->apply_to_all_products) {
            return true;
        }

        return $this->products()->where('products.id', $product->id)->exists();
    }

    public function calculateDiscountAmount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return round($amount * ($this->value / 100), 2);
        }

        // Type: fixed_amount
        return min($this->value, $amount);
    }

    public function applyToPrice(float $price): float
    {
        $discountAmount = $this->calculateDiscountAmount($price);
        return max(0, $price - $discountAmount);
    }

    public function incrementUsage(): void
    {
        $this->increment('current_usage');
    }

    public function meetsMinimumPurchase(float $amount): bool
    {
        if (!$this->min_purchase_amount) {
            return true;
        }

        return $amount >= $this->min_purchase_amount;
    }

    // Accessors
    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date < now();
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date > now();
    }

    public function getIsCurrentAttribute(): bool
    {
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percentage') {
            return $this->value . '%';
        }

        return number_format($this->value, 2, ',', ' ') . ' €';
    }

    public function getRemainingUsageAttribute(): ?int
    {
        if (!$this->max_usage) {
            return null;
        }

        return max(0, $this->max_usage - $this->current_usage);
    }
}