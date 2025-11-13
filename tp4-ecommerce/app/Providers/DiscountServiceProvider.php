<?php

namespace app\Services;

use app\Models\Discount;
use app\Models\DiscountCodes;
use app\Models\Product;
use Illuminate\Support\Collection;

class DiscountService
{
    /**
     * Récupère toutes les remises actives pour un produit donné
     */
    public function getActiveDiscountsForProduct(Product $product): Collection
    {
        return Discount::active()
            ->available()
            ->forProduct($product->id)
            ->orderBy('priority', 'desc')
            ->get()
            ->filter(fn($discount) => $discount->canApplyToProduct($product));
    }

    /**
     * Calcule le meilleur prix avec remise pour un produit
     */
    public function getBestPriceForProduct(Product $product): array
    {
        $discounts = $this->getActiveDiscountsForProduct($product);
        
        if ($discounts->isEmpty()) {
            return [
                'original_price' => $product->price,
                'final_price' => $product->price,
                'discount_amount' => 0,
                'discount' => null,
            ];
        }

        $bestDiscount = null;
        $lowestPrice = $product->price;
        $highestDiscountAmount = 0;

        foreach ($discounts as $discount) {
            $discountedPrice = $discount->applyToPrice($product->price);
            $discountAmount = $product->price - $discountedPrice;

            if ($discountedPrice < $lowestPrice) {
                $lowestPrice = $discountedPrice;
                $bestDiscount = $discount;
                $highestDiscountAmount = $discountAmount;
            }
        }

        return [
            'original_price' => $product->price,
            'final_price' => $lowestPrice,
            'discount_amount' => $highestDiscountAmount,
            'discount' => $bestDiscount,
        ];
    }

    /**
     * Valide et applique un code promo
     */
    public function validateDiscountCode(string $code, ?int $userId = null, ?float $cartTotal = null): array
    {
        $discountCode = DiscountCodes::where('code', strtoupper($code))
            ->with('discount')
            ->first();

        if (!$discountCode) {
            return [
                'valid' => false,
                'message' => 'Code promo invalide',
            ];
        }

        if (!$discountCode->isValid()) {
            return [
                'valid' => false,
                'message' => 'Ce code promo n\'est plus valide',
            ];
        }

        if (!$discountCode->canBeUsedByUser($userId)) {
            return [
                'valid' => false,
                'message' => 'Vous avez déjà utilisé ce code le nombre maximum de fois',
            ];
        }

        $discount = $discountCode->discount;

        if ($cartTotal !== null && !$discount->meetsMinimumPurchase($cartTotal)) {
            return [
                'valid' => false,
                'message' => 'Montant minimum d\'achat non atteint : ' . 
                            number_format($discount->min_purchase_amount, 2, ',', ' ') . ' €',
            ];
        }

        return [
            'valid' => true,
            'discount_code' => $discountCode,
            'discount' => $discount,
            'message' => 'Code promo appliqué avec succès',
        ];
    }

    /**
     * Calcule le total du panier avec remises
     */
    public function calculateCartTotal(Collection $cartItems, ?string $discountCode = null, ?int $userId = null): array
    {
        $subtotal = 0;
        $itemsWithDiscounts = [];

        // Calculer le sous-total avec les remises automatiques par produit
        foreach ($cartItems as $item) {
            $product = $item->product;
            $quantity = $item->quantity;
            
            $priceInfo = $this->getBestPriceForProduct($product);
            
            $itemTotal = $priceInfo['final_price'] * $quantity;
            $itemOriginalTotal = $priceInfo['original_price'] * $quantity;
            
            $itemsWithDiscounts[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $priceInfo['original_price'],
                'unit_final_price' => $priceInfo['final_price'],
                'item_total' => $itemTotal,
                'item_discount' => $itemOriginalTotal - $itemTotal,
                'discount' => $priceInfo['discount'],
            ];
            
            $subtotal += $itemTotal;
        }

        $result = [
            'items' => $itemsWithDiscounts,
            'subtotal' => $subtotal,
            'code_discount_amount' => 0,
            'total' => $subtotal,
            'discount_code' => null,
        ];

        // Appliquer le code promo si fourni
        if ($discountCode) {
            $validation = $this->validateDiscountCode($discountCode, $userId, $subtotal);
            
            if ($validation['valid']) {
                $discount = $validation['discount'];
                $codeDiscountAmount = $discount->calculateDiscountAmount($subtotal);
                
                $result['code_discount_amount'] = $codeDiscountAmount;
                $result['total'] = max(0, $subtotal - $codeDiscountAmount);
                $result['discount_code'] = $validation['discount_code'];
            } else {
                $result['code_error'] = $validation['message'];
            }
        }

        return $result;
    }

    /**
     * Enregistre l'utilisation d'un code promo après une commande
     */
    public function recordDiscountCodeUsage(
        DiscountCodes $discountCode,
        ?int $userId,
        ?int $orderId,
        float $discountAmount
    ): void {
        $discountCode->recordUsage($userId, $orderId, $discountAmount);
    }
}