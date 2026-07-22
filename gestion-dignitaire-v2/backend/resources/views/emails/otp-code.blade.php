@extends('emails.layout')

@section('subject', $purpose === 'inscription' ? 'Vérifiez votre adresse email' : 'Votre code de connexion')

@section('content')
  <h2 style="color:#16a34a; margin-top:0;">Bonjour {{ $prenom }},</h2>
  @if($purpose === 'inscription')
    <p>Merci de confirmer votre adresse email pour finaliser votre inscription. Voici votre code de vérification :</p>
  @else
    <p>Voici votre code de connexion à usage unique :</p>
  @endif
  <p style="text-align:center; margin:28px 0;">
    <span style="display:inline-block; background-color:#f0fdf4; border:2px solid #16a34a; color:#15803d; font-size:32px; font-weight:bold; letter-spacing:8px; padding:16px 24px; border-radius:8px;">
      {{ $code }}
    </span>
  </p>
  <p style="color:#6b7280; font-size:13px;">Ce code expire dans 5 minutes. Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.</p>
  <p style="margin-top:24px;">Cordialement,<br>L'équipe Gestion Dignitaires</p>
@endsection
