<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 * schema="Cart",
 * title="Cart",
 * description="Modèle de données pour un panier d'achat.",
 * @OA\Property(
 * property="id",
 * type="integer",
 * format="int64",
 * description="ID unique du panier."
 * ),
 * @OA\Property(
 * property="user_id",
 * type="integer",
 * format="int64",
 * description="ID de l'utilisateur associé à ce panier."
 * ),
 * @OA\Property(
 * property="items",
 * type="array",
 * description="Liste des articles dans le panier.",
 * @OA\Items(
 * ref="#/components/schemas/CartItem"
 * )
 * ),
 * @OA\Property(
 * property="total_price",
 * type="number",
 * format="float",
 * description="Prix total du panier."
 * ),
 * example={
 * "id": 1,
 * "user_id": 101,
 * "items": {},
 * "total_price": 49.99
 * }
 * )
 */
class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
