@extends('emails.layout')

@section('subject', 'Nouvelle recommandation sur votre candidature')

@section('content')
  <h2 style="color:#1d4ed8; margin-top:0;">Bonjour {{ $candidat->prenom }},</h2>
  <p>L'équipe en charge de l'examen de votre candidature vous a laissé une recommandation :</p>
  <div style="background-color:#eff6ff; border-left:4px solid #1d4ed8; padding:12px 16px; margin:16px 0; border-radius:4px; white-space:pre-line;">{{ $contenu }}</div>
  <p>Vous pouvez consulter et répondre à ce message depuis votre espace candidat.</p>
  <p style="margin-top:24px;">Cordialement,<br>L'équipe Gestion Dignitaires</p>
@endsection
