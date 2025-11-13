<?php

namespace App\Services;

use App\Models\Cart;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Service de gestion des remises
 * 
 * VERSION TEMPORAIRE pour E (Commandes)
 * F implémentera la vraie logique avec la table discounts
 */
class DiscountService
{
    /**
     * Appliquer un code promo au panier
     * 
     * VERSION ACTUELLE: Retourne 0 (aucune remise)
     * F remplacera cette méthode par la vraie logique
     * 
     * @param Cart $cart Le panier de l'utilisateur
     * @param string $code Le code promo à appliquer
     * @return float Le montant de la remise en FCFA
     * @throws Exception Si le code est invalide (pour la version finale)
     */
    public function apply(Cart $cart, string $code): float
    {
        // Log pour traçabilité (utile pour le debug)
        Log::info('DiscountService::apply called', [
            'cart_id' => $cart->id,
            'user_id' => $cart->user_id,
            'code' => $code,
            'items_count' => $cart->items->count(),
        ]);

        // ============================================
        // VERSION TEMPORAIRE (E)
        // ============================================
        // Pour permettre à E de travailler sans bloquer,
        // on retourne 0 (aucune remise)
        
        // TODO: F implémentera cette logique:
        // 
        // 1. Vérifier que le code existe dans la table discount_codes
        //    $discount = DiscountCode::where('code', $code)->first();
        //    if (!$discount) throw new Exception("Code promo invalide");
        //
        // 2. Vérifier les dates de validité
        //    if ($discount->start_date > now()) throw new Exception("Code pas encore valide");
        //    if ($discount->end_date < now()) throw new Exception("Code expiré");
        //
        // 3. Vérifier le nombre d'utilisations
        //    if ($discount->current_usage >= $discount->max_usage) {
        //        throw new Exception("Code promo épuisé");
        //    }
        //
        // 4. Calculer le montant de la remise selon le type
        //    $subtotal = $cart->items->sum(fn($item) => $item->variant->price * $item->quantity);
        //    
        //    if ($discount->type === 'percentage') {
        //        $discountAmount = ($subtotal * $discount->value) / 100;
        //    } else {
        //        $discountAmount = $discount->value; // Montant fixe
        //    }
        //
        // 5. Enregistrer l'utilisation dans discount_code_usage
        //    DiscountCodeUsage::create([
        //        'discount_code_id' => $discount->id,
        //        'user_id' => $cart->user_id,
        //        'used_at' => now(),
        //    ]);
        //    $discount->increment('current_usage');
        //
        // 6. Retourner le montant de la remise
        //    return $discountAmount;

        Log::info('Discount not applied (waiting for F implementation)', [
            'code' => $code,
        ]);

        return 0; // Aucune remise pour l'instant
    }

    /**
     * Valider un code promo sans l'appliquer
     * Utile pour afficher un message "Code valide" dans le frontend
     * 
     * @param string $code
     * @return bool
     */
    public function validate(string $code): bool
    {
        // TODO: F implémentera cette méthode
        // 
        // $discount = DiscountCode::where('code', $code)->first();
        // 
        // if (!$discount) return false;
        // if ($discount->start_date > now()) return false;
        // if ($discount->end_date < now()) return false;
        // if ($discount->current_usage >= $discount->max_usage) return false;
        // 
        // return true;

        Log::info('DiscountService::validate called', ['code' => $code]);
        
        return false; // Tous les codes sont invalides pour l'instant
    }

    /**
     * Obtenir les informations d'un code promo
     * Permet au frontend d'afficher "10% de réduction" avant application
     * 
     * @param string $code
     * @return array|null ['type' => 'percentage', 'value' => 10, 'description' => '...']
     */
    public function getCodeInfo(string $code): ?array
    {
        // TODO: F implémentera cette méthode
        // 
        // $discount = DiscountCode::where('code', $code)->first();
        // 
        // if (!$discount) return null;
        // 
        // return [
        //     'type' => $discount->type,
        //     'value' => $discount->value,
        //     'description' => $discount->description,
        //     'valid_until' => $discount->end_date,
        // ];

        Log::info('DiscountService::getCodeInfo called', ['code' => $code]);

        return null; // Aucune info disponible pour l'instant
    }

    /**
     * Calculer le montant d'une remise sans l'appliquer (simulation)
     * Utile pour afficher "Vous économiserez X FCFA" avant le checkout
     * 
     * @param Cart $cart
     * @param string $code
     * @return float
     */
    public function calculateDiscount(Cart $cart, string $code): float
    {
        // TODO: F implémentera cette méthode
        // Même logique que apply() mais sans enregistrer l'utilisation

        Log::info('DiscountService::calculateDiscount called', [
            'cart_id' => $cart->id,
            'code' => $code,
        ]);

        return 0; // Aucune remise calculée pour l'instant
    }
}