@component('mail::layout')
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            <div style="text-align: center; background: linear-gradient(135deg, #ff8c00, #ff6b00); padding: 30px 20px; color: white; border-radius: 8px 8px 0 0;">
                <div style="font-size: 28px; font-weight: 700; margin-bottom: 10px;">TropDouxRecup</div>
                <h1 style="font-size: 24px; font-weight: 600; margin: 0;">Vérification de votre compte</h1>
            </div>
        @endcomponent
    @endslot

# Bonjour {{ $marchand->nom_marchand }},

Votre code de vérification est :

<div style="text-align: center; margin: 30px 0; padding: 20px; background-color: #fff9f0; border-radius: 8px; border-left: 4px solid #ff8c00;">
    <div style="font-size: 16px; color: #666; margin-bottom: 10px;">Code de vérification</div>
    <div style="font-size: 36px; font-weight: 700; letter-spacing: 8px; color: #ff6b00; padding: 10px; background-color: #fff3e0; border-radius: 6px; display: inline-block; margin: 10px 0;">
        {{ $otp }}
    </div>
    <div style="color: #777; font-size: 14px; margin-top: 10px;">
        Ce code est valable pendant 10 minutes.
    </div>
</div>

Merci pour votre inscription.

@slot('footer')
    @component('mail::footer')
        <div style="background-color: #f5f5f5; padding: 20px; text-align: center; border-top: 1px solid #eee; color: #777; font-size: 14px;">
            <p style="margin: 5px 0;">© {{ date('Y') }} Votre Application. Tous droits réservés.</p>
            <p style="margin: 5px 0;">Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    @endcomponent
@endslot
@endcomponent