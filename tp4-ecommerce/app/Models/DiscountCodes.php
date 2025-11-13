<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DiscountCodes extends Model
{
    use HasFactory;

    protected $fillable = [
        'discount_id',
        'code',
        'max_uses',
        'max_uses_per_user',
        'current_uses',
        'is_active',
    ];

    protected $casts = [
        'max_uses' => 'integer',
        'max_uses_per_user' => 'integer',
        'current_uses' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relations
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(DiscountCodeUsage::class, 'discount_code_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where(function($q) {
            $q->whereNull('max_uses')
              ->orWhereColumn('current_uses', '<', 'max_uses');
        });
    }

    // Méthodes métier
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->discount || !$this->discount->isValid()) {
            return false;
        }

        if ($this->max_uses && $this->current_uses >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function canBeUsedByUser(?int $userId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if (!$userId) {
            return true;
        }

        $userUsageCount = $this->usages()
            ->where('user_id', $userId)
            ->count();

        return $userUsageCount < $this->max_uses_per_user;
    }

    public function recordUsage(?int $userId, ?int $orderId, float $discountAmount, ?string $ipAddress = null): DiscountCodeUsage
    {
        $usage = $this->usages()->create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);

        $this->increment('current_uses');
        $this->discount->incrementUsage();

        return $usage;
    }

    public function getUserUsageCount(?int $userId): int
    {
        if (!$userId) {
            return 0;
        }

        return $this->usages()->where('user_id', $userId)->count();
    }

    // Accessors
    public function getRemainingUsesAttribute(): ?int
    {
        if (!$this->max_uses) {
            return null;
        }

        return max(0, $this->max_uses - $this->current_uses);
    }

    // Méthode statique pour générer un code unique
    public static function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}