@component('mail::message')
# Bonjour {{ $marchand->nom_marchand }},

Votre code de vérification est :

# **{{ $otp }}**

Ce code est valable pendant 10 minutes.

Merci pour votre inscription.

@component('mail::footer')
© {{ date('Y') }} Votre Application. Tous droits réservés.
@endcomponent
@endcomponent
