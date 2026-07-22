@extends('emails.layout')

@section('subject', 'Réinitialisation de votre mot de passe')

@section('content')
  <h2 style="color:#16a34a; margin-top:0;">Bonjour {{ $prenom }},</h2>
  <p>Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour en choisir un nouveau :</p>
  <p style="text-align:center; margin:28px 0;">
    <a href="{{ $resetUrl }}" style="background: linear-gradient(90deg, #16a34a 0%, #2563eb 100%); color:#ffffff; text-decoration:none; padding:12px 28px; border-radius:8px; font-weight:bold; display:inline-block;">
      Réinitialiser mon mot de passe
    </a>
  </p>
  <p style="color:#6b7280; font-size:13px;">Ce lien expire dans 60 minutes. Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email — votre mot de passe restera inchangé.</p>
  <p style="margin-top:24px;">Cordialement,<br>L'équipe Gestion Dignitaires</p>
@endsection
