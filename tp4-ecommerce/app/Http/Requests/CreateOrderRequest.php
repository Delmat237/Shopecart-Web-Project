<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour la création de commande (checkout)
 */
class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth déjà gérée par middleware
    }

    public function rules(): array
    {
        return [
            'discount_code' => 'nullable|string|max:50',
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.required' => 'L\'adresse de livraison est obligatoire',
            'phone.required' => 'Le numéro de téléphone est obligatoire',
        ];
    }
}

/**
 * Validation pour la mise à jour du statut (admin)
 */
class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'ADMIN' || $this->user()->role === 'SUPERADMIN';
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:pending,paid,preparing,shipped,delivered,canceled',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Le statut est obligatoire',
            'status.in' => 'Le statut doit être: pending, paid, preparing, shipped, delivered, ou canceled',
        ];
    }
}