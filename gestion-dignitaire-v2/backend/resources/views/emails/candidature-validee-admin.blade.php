@extends('emails.layout')

@section('subject', 'Candidature validée')

@section('content')
  <h2 style="color:#16a34a; margin-top:0;">Candidature validée</h2>
  <p>La candidature de <strong>{{ $candidat->prenom }} {{ $candidat->nom }}</strong> ({{ $candidat->email }}) a été validée et un dignitaire a été créé.</p>
  <p style="margin-top:24px;">
    <a href="{{ $lienDignitaire }}" style="background-color:#16a34a; color:#ffffff; padding:10px 20px; border-radius:6px; text-decoration:none; display:inline-block;">
      Voir la fiche dignitaire
    </a>
  </p>
  <p style="margin-top:24px; color:#6b7280; font-size:12px;">Vous recevez cet email car les notifications de candidature sont activées dans Paramètres.</p>
@endsection
