@extends('emails.layout')

@section('subject', 'Nouvelle candidature soumise')

@section('content')
  <h2 style="color:#2563eb; margin-top:0;">Nouvelle candidature</h2>
  <p><strong>{{ $candidat->prenom }} {{ $candidat->nom }}</strong> vient de soumettre son dossier de candidature ({{ $candidat->email }}).</p>
  <p style="margin-top:24px;">
    <a href="{{ $lienCandidature }}" style="background-color:#2563eb; color:#ffffff; padding:10px 20px; border-radius:6px; text-decoration:none; display:inline-block;">
      Consulter le dossier
    </a>
  </p>
  <p style="margin-top:24px; color:#6b7280; font-size:12px;">Vous recevez cet email car les notifications de nouvelle candidature sont activées dans Paramètres.</p>
@endsection
