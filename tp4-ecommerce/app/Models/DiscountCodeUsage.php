<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountCodeUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_code_id',
        'user_id',
        'order_id',
        'discount_amount',
        'ip_address',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    // Relations
    public function discountCode(): BelongsTo
    {
        return $this->belongsTo(DiscountCodes::class, 'discount_code_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Accessors
    public function getFormattedDiscountAmountAttribute(): string
    {
        return number_format($this->discount_amount, 2, ',', ' ') . ' €';
    }
}