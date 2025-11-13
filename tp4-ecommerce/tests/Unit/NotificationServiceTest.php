<?php

namespace Tests\Unit;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    public function test_can_send_order_confirmation_email()
    {
        Mail::fake();

        $service = app(NotificationService::class);
        $service->sendOrderConfirmation('leonel.azangue@facsciences-uy1.cm', 100, 30000);

        Mail::assertSent(\App\Mail\GenericNotificationMail::class, function ($mail) {
            return $mail->to[0]['address'] === 'leonel.azangue@facsciences-uy1.cm' &&
                   $mail->subjectLine === 'Commande #100 Confirmée' &&
                   str_contains($mail->bodyContent, '30 000 FCFA');
        });
    }

    public function test_can_send_payment_failed_email()
    {
        Mail::fake();

        $service = app(NotificationService::class);
        $service->sendPaymentFailed('leonel.azangue@facsciences-uy1.cm', 100, 'Fond insuffisant');

        Mail::assertSent(\App\Mail\GenericNotificationMail::class, function ($mail) {
            return str_contains($mail->subjectLine, 'Paiement échoué') &&
                   str_contains($mail->bodyContent, 'Fond insuffisant');
        });
    }
}