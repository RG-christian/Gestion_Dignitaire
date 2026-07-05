@extends('emails.layout')

@section('subject', 'Candidature validée')

@section('content')
  <h2 style="color:#16a34a; margin-top:0;">Félicitations, {{ $candidat->prenom }} !</h2>
  <p>Votre candidature a été <strong>validée</strong>. Vous êtes désormais officiellement enregistré(e) en tant que dignitaire de la République Gabonaise.</p>
  <p>Vous pouvez vous connecter à votre espace personnel pour consulter votre dossier.</p>
  <p style="margin-top:24px;">Bienvenue,<br>L'équipe Gestion Dignitaires</p>
@endsection
