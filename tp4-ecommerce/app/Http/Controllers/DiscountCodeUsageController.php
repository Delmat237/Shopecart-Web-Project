<?php

namespace App\Http\Controllers;

use App\Models\DiscountCodeUsage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DiscountCodeUsageController extends Controller
{
    /**
     * Liste toutes les utilisations de codes promo
     */
    public function index(Request $request): JsonResponse
    {
        $query = DiscountCodeUsage::with([
            'discountCode.discount',
            'user',
            'order'
        ]);

        // Filtre par code promo
        if ($request->has('discount_code_id')) {
            $query->where('discount_code_id', $request->discount_code_id);
        }

        // Filtre par utilisateur
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtre par commande
        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        // Filtre par période
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $usages = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($usages);
    }

    /**
     * Affiche une utilisation spécifique
     */
    public function show(DiscountCodeUsage $discountCodeUsage): JsonResponse
    {
        $discountCodeUsage->load([
            'discountCode.discount',
            'user',
            'order.orderItems.product'
        ]);

        return response()->json($discountCodeUsage);
    }

    /**
     * Statistiques d'utilisation des codes promo
     */
    public function statistics(Request $request): JsonResponse
    {
        $query = DiscountCodeUsage::query();

        // Filtre par période
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $totalUsages = $query->count();
        $totalDiscountAmount = $query->sum('discount_amount');
        $uniqueUsers = $query->distinct('user_id')->count('user_id');
        $averageDiscountAmount = $totalUsages > 0 
            ? $totalDiscountAmount / $totalUsages 
            : 0;

        // Top 5 des codes les plus utilisés
        $topCodes = DiscountCodeUsage::with('discountCode')
            ->selectRaw('discount_code_id, COUNT(*) as usage_count, SUM(discount_amount) as total_amount')
            ->groupBy('discount_code_id')
            ->orderBy('usage_count', 'desc')
            ->limit(5)
            ->get();

        // Utilisations par jour (derniers 30 jours)
        $usagesByDay = DiscountCodeUsage::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(discount_amount) as amount')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'total_usages' => $totalUsages,
            'total_discount_amount' => round($totalDiscountAmount, 2),
            'unique_users' => $uniqueUsers,
            'average_discount_amount' => round($averageDiscountAmount, 2),
            'top_codes' => $topCodes,
            'usages_by_day' => $usagesByDay,
        ]);
    }

    /**
     * Historique d'utilisation pour un utilisateur spécifique
     */
    public function userHistory(Request $request): JsonResponse
    {
        $userId = $request->user_id ?? auth()->id();

        if (!$userId) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $usages = DiscountCodeUsage::with([
            'discountCode.discount',
            'order'
        ])
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        $totalSaved = DiscountCodeUsage::where('user_id', $userId)
            ->sum('discount_amount');

        return response()->json([
            'usages' => $usages,
            'total_saved' => round($totalSaved, 2),
        ]);
    }

    /**
     * Vérifie si un utilisateur a déjà utilisé un code spécifique
     */
    public function checkUserUsage(Request $request): JsonResponse
    {
        $request->validate([
            'discount_code_id' => 'required|exists:discount_codes,id',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $userId = $request->user_id ?? auth()->id();

        if (!$userId) {
            return response()->json([
                'has_used' => false,
                'usage_count' => 0,
            ]);
        }

        $usageCount = DiscountCodeUsage::where('discount_code_id', $request->discount_code_id)
            ->where('user_id', $userId)
            ->count();

        $usages = DiscountCodeUsage::with('order')
            ->where('discount_code_id', $request->discount_code_id)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'has_used' => $usageCount > 0,
            'usage_count' => $usageCount,
            'usages' => $usages,
        ]);
    }

    /**
     * Export des utilisations (CSV)
     */
    public function export(Request $request)
    {
        $query = DiscountCodeUsage::with([
            'discountCode.discount',
            'user',
            'order'
        ]);

        // Appliquer les mêmes filtres que pour index
        if ($request->has('discount_code_id')) {
            $query->where('discount_code_id', $request->discount_code_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $usages = $query->orderBy('created_at', 'desc')->get();

        $csv = "ID,Code Promo,Remise,Utilisateur,Email,Commande,Montant Remise,IP,Date\n";
        
        foreach ($usages as $usage) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%.2f,%s,%s\n",
                $usage->id,
                $usage->discountCode->code ?? 'N/A',
                $usage->discountCode->discount->name ?? 'N/A',
                $usage->user->name ?? 'Invité',
                $usage->user->email ?? 'N/A',
                $usage->order_id ?? 'N/A',
                $usage->discount_amount,
                $usage->ip_address ?? 'N/A',
                $usage->created_at->format('Y-m-d H:i:s')
            );
        }

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="discount_usages_' . now()->format('Y-m-d') . '.csv"');
    }
}