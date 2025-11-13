@component('mail::message')
# Votre commande est confirmée !

Bonjour **{{ $order->user->name }},

Nous avons bien reçu votre commande **#{{ $order->id }}** du **{{ $order->created_at->format('d/m/Y à H:i') }}**.

@component('mail::table')
| Produit | Quantité | Prix unitaire | Total |
|---------|----------|---------------|-------|
@foreach($order->items as $item)
| {{ $item->variant->product->name }} ({{ $item->variant->color ?? 'N/A' }}) | {{ $item->quantity }} | {{ number_format($item->price, 0, ',', ' ') }} FCFA | {{ number_format($item->quantity * $item->price, 0, ',', ' ') }} FCFA |
@endforeach
@endcomponent

@component('mail::panel')
**Total : {{ number_format($order->total, 0, ',', ' ') }} FCFA**
@endcomponent

Nous vous enverrons un email dès que votre colis sera expédié.

@component('mail::button', ['url' => url('/orders/' . $order->id)])
Voir ma commande
@endcomponent

Merci de faire confiance à Shopcart !  
L'équipe Shopcart Cameroon
@endcomponent