@extends('emails.layout')

@section('subject', 'Nouvelle nomination')

@section('content')
  <h2 style="color:#2563eb; margin-top:0;">Bonjour {{ $dignitaire->prenom }},</h2>
  <p>Une nouvelle nomination vient d'être enregistrée à votre dossier :</p>
  <div style="background-color:#eff6ff; border-left:4px solid #2563eb; padding:12px 16px; margin:16px 0; border-radius:4px;">
    @if($nomination->fonction)
      <div><strong>Fonction :</strong> {{ $nomination->fonction }}</div>
    @endif
    @if($entiteNom)
      <div><strong>Entité :</strong> {{ $entiteNom }}</div>
    @endif
    @if($nomination->date_debut)
      <div><strong>Date de début :</strong> {{ \Carbon\Carbon::parse($nomination->date_debut)->format('d/m/Y') }}</div>
    @endif
  </div>
  <p style="margin-top:24px;">Cordialement,<br>L'équipe Gestion Dignitaires</p>
@endsection
