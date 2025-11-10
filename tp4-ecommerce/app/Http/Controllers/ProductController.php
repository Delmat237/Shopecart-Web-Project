<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Tag(
 * name="Products",
 * description="Opérations sur les produits de la boutique."
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/products",
     * operationId="getProductsList",
     * tags={"Products"},
     * summary="Obtenir la liste complète des produits",
     * description="Retourne tous les produits (peut nécessiter une pagination).",
     * @OA\Response(
     * response=200,
     * description="Liste des produits récupérée avec succès.",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     * )
     * )
     */
    public function index()
    {
        // ...
    }

    /**
     * @OA\Get(
     * path="/api/products/{id}",
     * operationId="getProductById",
     * tags={"Products"},
     * summary="Obtenir un produit par son ID",
     * description="Retourne les détails d'un produit spécifique.",
     * @OA\Parameter(
     * name="id",
     * description="ID du produit à récupérer",
     * required=true,
     * in="path",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Détails du produit récupérés.",
     * @OA\JsonContent(ref="#/components/schemas/Product")
     * ),
     * @OA\Response(
     * response=404,
     * description="Produit non trouvé."
     * )
     * )
     */
    public function show(string $id)
    {
        // ...
    }
    
    // ... Autres méthodes CRUD (create, store, edit, update, destroy) non documentées ici pour la concision.
}