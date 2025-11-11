<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 * name="Orders",
 * description="Opérations de gestion des commandes."
 * )
 */
class OrderController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/orders",
     * operationId="getOrdersList",
     * tags={"Orders"},
     * summary="Obtenir la liste des commandes de l'utilisateur",
     * description="Retourne toutes les commandes passées par l'utilisateur authentifié.",
     * security={{"bearerAuth": {}}},
     * @OA\Response(
     * response=200,
     * description="Liste des commandes récupérée avec succès.",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     * )
     * )
     */
    public function index()
    {
        // ...
    }

    /**
     * @OA\Post(
     * path="/api/orders",
     * operationId="storeOrder",
     * tags={"Orders"},
     * summary="Créer une nouvelle commande",
     * description="Passe une nouvelle commande en utilisant le contenu du panier de l'utilisateur.",
     * security={{"bearerAuth": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * @OA\Property(property="shipping_address", type="string", example="123 Rue de la Livraion"),
     * @OA\Property(property="payment_method", type="string", example="credit_card")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Commande créée avec succès.",
     * @OA\JsonContent(ref="#/components/schemas/Order")
     * ),
     * @OA\Response(
     * response=401,
     * description="Non autorisé."
     * )
     * )
     */
    public function store(Request $request)
    {
        // ...
    }
    


    /**
     * Display the specified resource.
     */
    public function show(Orders $orders)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orders $orders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orders $orders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orders $orders)
    {
        //
    }
}
