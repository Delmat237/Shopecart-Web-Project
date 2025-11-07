<?php

namespace App\Services;

use App\Mail\GenericNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send(string $to, string $subject, string $body): void
    {
        try {
            Log::info('Notification email ', [
                'to' => $to,
                'subject' => $subject
            ]);
            Mail::to($to)->send(new GenericNotificationMail($subject, $body));
        } catch (\Exception $e) {
            Log::error('Notification email failed', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendOrderConfirmation(string $email, int $orderId, float $total): void
    {
        $body = "Bonjour,\n\nVotre commande #{$orderId} a été confirmée.\n\nTotal : " . number_format($total, 0, ',', ' ') . " FCFA\n\nMerci pour votre achat !\n\nShopcart Cameroon";
        $this->send($email, "Commande #{$orderId} Confirmée", $body);
    }

    public function sendPaymentFailed(string $email, int $orderId, string $error): void
    {
        $body = "Bonjour,\n\nLe paiement de votre commande #{$orderId} a échoué.\n\nRaison : {$error}\n\nVeuillez réessayer.\n\nShopcart Cameroon";
        $this->send($email, "Paiement échoué - Commande #{$orderId}", $body);
    }

    public function sendDiscountApplied(string $email, int $orderId, string $code, float $discount): void
    {
        $body = "Félicitations !\n\nLe code promo **{$code}** a été appliqué sur votre commande #{$orderId}.\n\nRéduction : " . number_format($discount, 0, ',', ' ') . " FCFA\n\nShopcart Cameroon";
        $this->send($email, "Code promo appliqué !", $body);
    }
}