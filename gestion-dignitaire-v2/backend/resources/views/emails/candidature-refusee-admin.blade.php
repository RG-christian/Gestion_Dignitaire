@extends('emails.layout')

@section('subject', 'Candidature refusée')

@section('content')
  <h2 style="color:#dc2626; margin-top:0;">Candidature refusée</h2>
  <p>La candidature de <strong>{{ $candidat->prenom }} {{ $candidat->nom }}</strong> ({{ $candidat->email }}) a été refusée.</p>
  <div style="background-color:#fef2f2; border-left:4px solid #dc2626; padding:12px 16px; margin:16px 0; border-radius:4px;">
    <strong>Motif :</strong> {{ $motif }}
  </div>
  <p style="margin-top:24px; color:#6b7280; font-size:12px;">Vous recevez cet email car les notifications de candidature sont activées dans Paramètres.</p>
@endsection
