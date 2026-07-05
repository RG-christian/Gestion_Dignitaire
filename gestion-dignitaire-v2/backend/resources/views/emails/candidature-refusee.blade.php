@extends('emails.layout')

@section('subject', 'Candidature non retenue')

@section('content')
  <h2 style="color:#dc2626; margin-top:0;">Bonjour {{ $candidat->prenom }},</h2>
  <p>Après examen de votre dossier, votre candidature n'a malheureusement pas été retenue.</p>
  @if($motif)
    <div style="background-color:#fef2f2; border-left:4px solid #dc2626; padding:12px 16px; margin:16px 0; border-radius:4px;">
      <strong>Motif :</strong> {{ $motif }}
    </div>
  @endif
  <p>Vous pouvez soumettre une nouvelle candidature en complétant les éléments manquants de votre dossier.</p>
  <p style="margin-top:24px;">Cordialement,<br>L'équipe Gestion Dignitaires</p>
@endsection
