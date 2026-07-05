@extends('emails.layout')

@section('subject', 'Votre mandat arrive à échéance')

@section('content')
  <h2 style="color:#ca8a04; margin-top:0;">Bonjour {{ $dignitaire->prenom }},</h2>
  <p>Nous vous informons que votre mandat arrive prochainement à échéance :</p>
  <div style="background-color:#fefce8; border-left:4px solid #ca8a04; padding:12px 16px; margin:16px 0; border-radius:4px;">
    @if($nomination->fonction)
      <div><strong>Fonction :</strong> {{ $nomination->fonction }}</div>
    @endif
    <div><strong>Date de fin :</strong> {{ \Carbon\Carbon::parse($nomination->date_fin)->format('d/m/Y') }}</div>
    <div><strong>Jours restants :</strong> {{ $joursRestants }}</div>
  </div>
  <p>Merci de vous rapprocher de votre administration pour les démarches nécessaires avant cette échéance.</p>
  <p style="margin-top:24px;">Cordialement,<br>L'équipe Gestion Dignitaires</p>
@endsection
