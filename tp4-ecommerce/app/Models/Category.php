<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'parent_id', 'position', 'is_visible',
        'meta_title', 'meta_description'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    // Relation parent
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relation enfants
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relation produits
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Scope catégories visibles
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    // Scope catégories racine
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}