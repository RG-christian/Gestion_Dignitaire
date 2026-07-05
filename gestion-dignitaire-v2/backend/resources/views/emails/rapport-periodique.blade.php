@extends('emails.layout')

@section('subject', "Rapport {$rapport->type} disponible")

@section('content')
  <h2 style="color:#2563eb; margin-top:0;">Rapport {{ $rapport->type }} disponible</h2>
  <p>Le rapport de synthèse pour la période du <strong>{{ $rapport->periode_debut->format('d/m/Y') }}</strong> au <strong>{{ $rapport->periode_fin->format('d/m/Y') }}</strong> vient d'être généré automatiquement.</p>
  <div style="background-color:#eff6ff; border-left:4px solid #2563eb; padding:12px 16px; margin:16px 0; border-radius:4px;">
    <div><strong>Type :</strong> {{ ucfirst($rapport->type) }}</div>
    <div><strong>Généré le :</strong> {{ $rapport->genere_le->format('d/m/Y à H:i') }}</div>
  </div>
  <p>Vous trouverez le rapport complet en pièce jointe. Il reste également consultable depuis l'application, dans la page "Rapports".</p>
  <p style="margin-top:24px;">Cordialement,<br>L'équipe Gestion Dignitaires</p>
@endsection
