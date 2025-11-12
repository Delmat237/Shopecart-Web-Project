<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="Liste des produits",
     *     description="Récupère la liste paginée des produits avec possibilité de filtrer",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filtrer par ID de catégorie",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="shelf",
     *         in="query",
     *         description="Filtrer par ID de rayon",
     *         required=false,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Recherche textuelle dans le nom et la description",
     *         required=false,
     *         @OA\Schema(type="string", example="smartphone")
     *     ),
     *     @OA\Parameter(
     *         name="price_min",
     *         in="query",
     *         description="Prix minimum",
     *         required=false,
     *         @OA\Schema(type="number", example=100)
     *     ),
     *     @OA\Parameter(
     *         name="price_max",
     *         in="query",
     *         description="Prix maximum",
     *         required=false,
     *         @OA\Schema(type="number", example=1500)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de page",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Nombre de résultats par page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15, example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des produits avec pagination",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="iPhone 15 Pro"),
     *                     @OA\Property(property="slug", type="string", example="iphone-15-pro"),
     *                     @OA\Property(property="description", type="string", example="Smartphone Apple dernière génération"),
     *                     @OA\Property(property="base_price", type="number", example=1199.99),
     *                     @OA\Property(property="image", type="string", example="products/iphone15.jpg"),
     *                     @OA\Property(property="image_url", type="string", example="http://localhost:8000/storage/products/iphone15.jpg"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="variants", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Titanium Blue - 256GB"),
     *                             @OA\Property(property="color", type="string", example="Blue"),
     *                             @OA\Property(property="size", type="string", example="256GB"),
     *                             @OA\Property(property="price", type="number", example=1199.99),
     *                             @OA\Property(property="stock", type="integer", example=50),
     *                             @OA\Property(property="sku", type="string", example="IPH15-BLU-256"),
     *                             @OA\Property(property="in_stock", type="boolean", example=true)
     *                         )
     *                     ),
     *                     @OA\Property(property="created_at", type="string", example="2025-11-12T10:30:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", example="2025-11-12T10:30:00.000000Z")
     *                 )
     *             ),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string", example="http://localhost:8000/api/v1/products?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://localhost:8000/api/v1/products?page=4"),
     *                 @OA\Property(property="prev", type="string", example=null),
     *                 @OA\Property(property="next", type="string", example="http://localhost:8000/api/v1/products?page=2")
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=4),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['variants'])
            ->active();

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('shelf')) {
            $query->byShelf($request->shelf);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('price_min') || $request->filled('price_max')) {
            $query->priceRange($request->price_min, $request->price_max);
        }

        $perPage = $request->input('per_page', 15);
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Créer un produit",
     *     description="Crée un nouveau produit avec ses variantes optionnelles",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "base_price"},
     *             @OA\Property(property="name", type="string", example="iPhone 15 Pro"),
     *             @OA\Property(property="slug", type="string", example="iphone-15-pro", description="Généré automatiquement si non fourni"),
     *             @OA\Property(property="description", type="string", example="Smartphone Apple dernière génération avec puce A17 Pro"),
     *             @OA\Property(property="base_price", type="number", format="float", example=1199.99),
     *             @OA\Property(property="category_id", type="integer", example=1, description="ID de la catégorie"),
     *             @OA\Property(property="shelf_id", type="integer", example=2, description="ID du rayon"),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="variants", type="array", description="Liste des variantes du produit",
     *                 @OA\Items(
     *                     @OA\Property(property="name", type="string", example="Titanium Blue - 256GB"),
     *                     @OA\Property(property="color", type="string", example="Blue"),
     *                     @OA\Property(property="size", type="string", example="256GB"),
     *                     @OA\Property(property="price", type="number", example=1199.99),
     *                     @OA\Property(property="stock", type="integer", example=50),
     *                     @OA\Property(property="sku", type="string", example="IPH15-BLU-256")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produit créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="iPhone 15 Pro"),
     *                 @OA\Property(property="slug", type="string", example="iphone-15-pro"),
     *                 @OA\Property(property="description", type="string", example="Smartphone Apple dernière génération"),
     *                 @OA\Property(property="base_price", type="number", example=1199.99),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="variants", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Titanium Blue - 256GB"),
     *                         @OA\Property(property="stock", type="integer", example=50)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Le nom du produit est requis"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="Le nom du produit est requis")
     *                 ),
     *                 @OA\Property(property="base_price", type="array",
     *                     @OA\Items(type="string", example="Le prix de base est requis")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        if (!empty($data['variants'])) {
            foreach ($data['variants'] as $variantData) {
                $product->variants()->create($variantData);
            }
        }

        return new ProductResource($product->load('variants'));
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{id}",
     *     summary="Détails d'un produit",
     *     description="Récupère les informations complètes d'un produit avec ses variantes",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du produit",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails du produit",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="iPhone 15 Pro"),
     *                 @OA\Property(property="slug", type="string", example="iphone-15-pro"),
     *                 @OA\Property(property="description", type="string", example="Smartphone Apple dernière génération"),
     *                 @OA\Property(property="base_price", type="number", example=1199.99),
     *                 @OA\Property(property="image_url", type="string", example="http://localhost:8000/storage/products/iphone15.jpg"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="variants", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Titanium Blue - 256GB"),
     *                         @OA\Property(property="color", type="string", example="Blue"),
     *                         @OA\Property(property="size", type="string", example="256GB"),
     *                         @OA\Property(property="price", type="number", example=1199.99),
     *                         @OA\Property(property="stock", type="integer", example=50),
     *                         @OA\Property(property="sku", type="string", example="IPH15-BLU-256"),
     *                         @OA\Property(property="in_stock", type="boolean", example=true)
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produit non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Produit non trouvé")
     *         )
     *     )
     * )
     */
    public function show(Product $product)
    {
        $product->load('variants');
        return new ProductResource($product);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{id}",
     *     summary="Modifier un produit",
     *     description="Met à jour les informations d'un produit existant",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du produit",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="iPhone 15 Pro Max"),
     *             @OA\Property(property="description", type="string", example="Smartphone Apple avec écran 6.7 pouces"),
     *             @OA\Property(property="base_price", type="number", format="float", example=1399.99),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produit modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="iPhone 15 Pro Max"),
     *                 @OA\Property(property="base_price", type="number", example=1399.99)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produit non trouvé"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     )
     * )
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        if (!empty($data['variants'])) {
            foreach ($data['variants'] as $variantData) {
                if (isset($variantData['id'])) {
                    $product->variants()->where('id', $variantData['id'])->update($variantData);
                } else {
                    $product->variants()->create($variantData);
                }
            }
        }

        return new ProductResource($product->fresh('variants'));
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{id}",
     *     summary="Supprimer un produit",
     *     description="Supprime définitivement un produit et ses variantes",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID du produit",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Produit supprimé avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produit non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Produit non trouvé")
     *         )
     *     )
     * )
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Produit supprimé avec succès'], 204);
    }
}