@component('mail::message')
# Paiement échoué

Bonjour **{{ $order->user->name }},

Le paiement de votre commande **#{{ $order->id }}** a échoué.

**Raison** : {{ $error }}

Veuillez réessayer avec une autre carte ou contacter votre banque.

@component('mail::button', ['url' => url('/checkout')])
Réessayer le paiement
@endcomponent

Merci,  
Shopcart Cameroon
@endcomponent