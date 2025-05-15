<x-mail::message>
# Votre code d'authentification

Bonjour {{ $name }},

Voici votre code d'authentification à deux facteurs :

<x-mail::panel>
<div style="font-size: 28px; text-align: center; letter-spacing: 5px; font-weight: bold;">{{ $code }}</div>
</x-mail::panel>

Ce code est valable jusqu'à {{ $expiresAt }} et ne peut être utilisé qu'une seule fois.

Si vous n'avez pas demandé ce code, veuillez ignorer cet email et sécuriser votre compte immédiatement en modifiant votre mot de passe.

<x-mail::button :url="route('password.request')">
Réinitialiser mon mot de passe
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>