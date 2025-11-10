<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Tag(
 * name="Cart",
 * description="Opérations sur le panier d'achat de l'utilisateur."
 * )
 */
class CartController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/cart",
     * operationId="getCartContent",
     * tags={"Cart"},
     * summary="Afficher le contenu du panier",
     * description="Retourne la liste des articles actuellement dans le panier de l'utilisateur.",
     * security={{"bearerAuth": {}}},
     * @OA\Response(
     * response=200,
     * description="Contenu du panier récupéré.",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CartItem"))
     * ),
     * @OA\Response(
     * response=401,
     * description="Non authentifié."
     * )
     * )
     */
    public function index()
    {
        // ...
    }

    /**
     * @OA\Post(
     * path="/api/v1/cart",
     * operationId="addItemToCart",
     * tags={"Cart"},
     * summary="Ajouter un produit au panier",
     * description="Ajoute un produit au panier ou met à jour la quantité si le produit existe déjà.",
     * security={{"bearerAuth": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"product_id", "quantity"},
     * @OA\Property(property="product_id", type="integer", example=10),
     * @OA\Property(property="quantity", type="integer", example=1)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Article ajouté/mis à jour dans le panier."
     * ),
     * @OA\Response(
     * response=401,
     * description="Non authentifié."
     * )
     * )
     */
    public function store(Request $request)
    {
        // ...
    }

    /**
     * @OA\Put(
     * path="/api/v1/cart/{productId}",
     * operationId="updateCartItemQuantity",
     * tags={"Cart"},
     * summary="Mettre à jour la quantité d'un article dans le panier",
     * description="Modifie la quantité d'un produit spécifique dans le panier. Utilisez 0 pour supprimer l'article.",
     * security={{"bearerAuth": {}}},
     * @OA\Parameter(
     * name="productId",
     * description="ID du produit dans le panier (ou ID de l'article du panier)",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"quantity"},
     * @OA\Property(property="quantity", type="integer", example=3)
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Quantité mise à jour."
     * ),
     * @OA\Response(
     * response=404,
     * description="Article non trouvé dans le panier."
     * )
     * )
     * * @OA\Server(
    * url="http://localhost:8000",
    * description="Serveur de l'API locale (Corrigé pour utiliser le port 8000)"
    * )
    */
    public function update(Request $request, string $id)
    {
        // ...
    }

    /**
     * @OA\Delete(
     * path="/api/v1/cart/{productId}",
     * operationId="removeCartItem",
     * tags={"Cart"},
     * summary="Supprimer un article du panier",
     * description="Supprime un produit spécifique du panier de l'utilisateur.",
     * security={{"bearerAuth": {}}},
     * @OA\Parameter(
     * name="productId",
     * description="ID du produit à supprimer",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="Article supprimé avec succès."
     * ),
     * @OA\Response(
     * response=404,
     * description="Article non trouvé dans le panier."
     * )
     * )
     */
    public function destroy(string $id)
    {
        // ...
    }
}