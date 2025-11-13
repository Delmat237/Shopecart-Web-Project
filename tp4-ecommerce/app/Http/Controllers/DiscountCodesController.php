<?php

namespace App\Http\Controllers;

use App\Models\DiscountCodes;
use App\Providers\DiscountServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiscountCodesController extends Controller
{
    protected DiscountService $discountService;

    public function __construct(DiscountService $discountService)
    {
        $this->discountService = $discountService;
    }

    /**
     * Liste tous les codes promo
     */
    public function index(Request $request): JsonResponse
    {
        $query = DiscountCodes::with('discount', 'usages');

        if ($request->has('active')) {
            $query->active();
        }

        if ($request->has('available')) {
            $query->available();
        }

        if ($request->has('discount_id')) {
            $query->where('discount_id', $request->discount_id);
        }

        $codes = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($codes);
    }

    /**
     * Crée un nouveau code promo
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'discount_id' => 'required|exists:discounts,id',
            'code' => 'nullable|string|max:50|unique:discount_codes,code',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        // Générer un code automatiquement si non fourni
        if (!isset($validated['code'])) {
            $validated['code'] = DiscountCodes::generateUniqueCode();
        } else {
            $validated['code'] = strtoupper($validated['code']);
        }

        $discountCode = DiscountCodes::create($validated);

        return response()->json([
            'message' => 'Code promo créé avec succès',
            'discount_code' => $discountCode->load('discount')
        ], 201);
    }

    /**
     * Affiche un code promo spécifique
     */
    public function show(DiscountCodes $discountCode): JsonResponse
    {
        return response()->json($discountCode->load('discount', 'usages.user', 'usages.order'));
    }

    /**
     * Met à jour un code promo
     */
    public function update(Request $request, DiscountCodes $discountCode): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'string|max:50|unique:discount_codes,code,' . $discountCode->id,
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'integer|min:1',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper($validated['code']);
        }

        $discountCode->update($validated);

        return response()->json([
            'message' => 'Code promo mis à jour avec succès',
            'discount_code' => $discountCode->load('discount')
        ]);
    }

    /**
     * Supprime un code promo
     */
    public function destroy(DiscountCodes $discountCode): JsonResponse
    {
        $discountCode->delete();

        return response()->json([
            'message' => 'Code promo supprimé avec succès'
        ]);
    }

    /**
     * Valide un code promo (pour l'application frontend)
     */
    public function validate(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'cart_total' => 'nullable|numeric|min:0',
        ]);

        $userId = auth()->id();
        $validation = $this->discountService->validateDiscountCode(
            $request->code,
            $userId,
            $request->cart_total
        );

        if (!$validation['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $validation['message']
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'message' => $validation['message'],
            'discount' => $validation['discount'],
            'discount_code' => $validation['discount_code'],
        ]);
    }

    /**
     * Active/désactive un code promo
     */
    public function toggle(DiscountCodes $discountCode): JsonResponse
    {
        $discountCode->update(['is_active' => !$discountCode->is_active]);

        return response()->json([
            'message' => 'Statut du code promo modifié',
            'discount_code' => $discountCode
        ]);
    }

    /**
     * Génère un nouveau code promo aléatoire
     */
    public function generate(): JsonResponse
    {
        $code = DiscountCodes::generateUniqueCode();

        return response()->json([
            'code' => $code
        ]);
    }
}