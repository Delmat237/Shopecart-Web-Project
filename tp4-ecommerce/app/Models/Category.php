<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 * schema="Category",
 * title="Category",
 * description="Modèle de données pour une catégorie de produits.",
 * @OA\Property(
 * property="id",
 * type="integer",
 * format="int64",
 * description="ID unique de la catégorie."
 * ),
 * @OA\Property(
 * property="name",
 * type="string",
 * description="Nom de la catégorie."
 * ),
 * @OA\Property(
 * property="slug",
 * type="string",
 * description="Slug pour URL."
 * ),
 * @OA\Property(
 * property="description",
 * type="string",
 * nullable=true,
 * description="Description de la catégorie."
 * ),
 * @OA\Property(
 * property="is_visible",
 * type="boolean",
 * description="Visibilité de la catégorie (true si visible)."
 * ),
 * example={
 * "id": 1,
 * "name": "Électronique",
 * "slug": "electronique",
 * "description": "Appareils et gadgets électroniques.",
 * "is_visible": true
 * }
 * )
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_visible',
        'position',
    ];
    
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}