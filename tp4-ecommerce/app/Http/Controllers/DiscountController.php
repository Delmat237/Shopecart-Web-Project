<?php

namespace app\Http\Controllers;

use app\Models\Discount;
use app\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class DiscountController extends Controller
{
    /**
     * Liste toutes les remises
     */
    public function index(Request $request): JsonResponse
    {
        $query = Discount::with('products', 'discountCodes');

        // Filtres
        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('available')) {
            $query->available();
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $discounts = $query->orderBy('priority', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return response()->json($discounts);
    }

    /**
     * Crée une nouvelle remise
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => ['required', Rule::in(['percentage', 'fixed_amount'])],
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'apply_to_all_products' => 'boolean',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_usage' => 'nullable|integer|min:1',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        // Validation spécifique selon le type
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return response()->json([
                'message' => 'Le pourcentage ne peut pas dépasser 100%'
            ], 422);
        }

        $productIds = $validated['product_ids'] ?? [];
        unset($validated['product_ids']);

        $discount = Discount::create($validated);

        // Attacher les produits si spécifiés
        if (!empty($productIds) && !$discount->apply_to_all_products) {
            $discount->products()->attach($productIds);
        }

        return response()->json([
            'message' => 'Remise créée avec succès',
            'discount' => $discount->load('products', 'discountCodes')
        ], 201);
    }

    /**
     * Affiche une remise spécifique
     */
    public function show(Discount $discount): JsonResponse
    {
        return response()->json($discount->load('products', 'discountCodes'));
    }

    /**
     * Met à jour une remise
     */
    public function update(Request $request, Discount $discount): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'type' => Rule::in(['percentage', 'fixed_amount']),
            'value' => 'numeric|min:0',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'is_active' => 'boolean',
            'priority' => 'integer|min:0',
            'apply_to_all_products' => 'boolean',
            'min_purchase_amount' => 'nullable|numeric|min:0',
            'max_usage' => 'nullable|integer|min:1',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        if (isset($validated['type']) && $validated['type'] === 'percentage' 
            && isset($validated['value']) && $validated['value'] > 100) {
            return response()->json([
                'message' => 'Le pourcentage ne peut pas dépasser 100%'
            ], 422);
        }

        $productIds = $validated['product_ids'] ?? null;
        unset($validated['product_ids']);

        $discount->update($validated);

        // Mettre à jour les produits si spécifiés
        if ($productIds !== null && !$discount->apply_to_all_products) {
            $discount->products()->sync($productIds);
        }

        return response()->json([
            'message' => 'Remise mise à jour avec succès',
            'discount' => $discount->load('products', 'discountCodes')
        ]);
    }

    /**
     * Supprime une remise
     */
    public function destroy(Discount $discount): JsonResponse
    {
        $discount->delete();

        return response()->json([
            'message' => 'Remise supprimée avec succès'
        ]);
    }

    /**
     * Active/désactive une remise
     */
    public function toggle(Discount $discount): JsonResponse
    {
        $discount->update(['is_active' => !$discount->is_active]);

        return response()->json([
            'message' => 'Statut de la remise modifié',
            'discount' => $discount
        ]);
    }

    /**
     * Récupère les remises actives pour un produit
     */
    public function forProduct(Product $product): JsonResponse
    {
        $discounts = Discount::active()
            ->available()
            ->forProduct($product->id)
            ->orderBy('priority', 'desc')
            ->get()
            ->filter(fn($discount) => $discount->canApplyToProduct($product));

        return response()->json($discounts);
    }
}