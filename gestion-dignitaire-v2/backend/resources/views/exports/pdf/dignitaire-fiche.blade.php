<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Fiche {{ $dignitaire->nom_complet }}</title>
<style>
    @page { margin: 25px 25px 55px 25px; }
    body { font-family: Arial, Helvetica, sans-serif; color: #1f2937; font-size: 11px; }

    #footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 30px; font-size: 9px; color: #6b7280; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 6px; }
    #footer:after { content: "Page " counter(page) " / " counter(pages); }

    .entete { background-color: #16a34a; color: #ffffff; border-radius: 6px; padding: 14px 18px; margin-bottom: 14px; }
    .entete table { width: 100%; }
    .entete .nom { font-size: 18px; font-weight: bold; }
    .entete .sous { font-size: 11px; opacity: .9; margin-top: 3px; }
    .entete .photo { width: 70px; height: 70px; border-radius: 6px; background-color: #ffffff; text-align: center; }
    .entete .photo img { width: 70px; height: 70px; object-fit: cover; border-radius: 6px; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; background-color: #eab308; color: #1f2937; font-size: 10px; font-weight: bold; }

    .section { margin-bottom: 14px; }
    .section .titre { background-color: #eab308; color: #1f2937; font-weight: bold; padding: 5px 10px; font-size: 12px; margin-bottom: 6px; }

    table.data { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    table.data th { background-color: #f3f4f6; text-align: left; padding: 5px 8px; font-size: 9.5px; border: 1px solid #d1d5db; }
    table.data td { padding: 5px 8px; border: 1px solid #e5e7eb; font-size: 9.5px; }

    table.infos td { padding: 3px 6px; font-size: 10.5px; vertical-align: top; }
    table.infos td.label { color: #6b7280; width: 160px; }
</style>
</head>
<body>
    <div id="footer">Gestion Dignitaires — République Gabonaise — Fiche générée le {{ $genereLe }}</div>

    <div class="entete">
        <table>
            <tr>
                @if($photoBase64)
                    <td style="width: 80px;">
                        <div class="photo"><img src="{{ $photoBase64 }}" alt="Photo"></div>
                    </td>
                @endif
                <td>
                    <div class="nom">{{ $dignitaire->nom_complet }}</div>
                    <div class="sous">NIP : {{ $dignitaire->nip ?? '—' }} &nbsp;|&nbsp; Matricule : {{ $dignitaire->matricule ?? '—' }}</div>
                    <div class="sous">{{ $dignitaire->genre ?? '' }} &nbsp;|&nbsp; {{ $dignitaire->etat_civil ?? '' }}</div>
                </td>
                <td style="width: 100px; text-align: right; vertical-align: top;">
                    <span class="badge">{{ ucfirst(str_replace('_', ' ', $dignitaire->statut ?? '')) }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="titre">Informations personnelles</div>
        <table class="infos">
            <tr>
                <td class="label">Date de naissance</td>
                <td>{{ $dignitaire->date_naissance?->format('d/m/Y') ?? '—' }}</td>
                <td class="label">Lieu de naissance</td>
                <td>{{ $dignitaire->lieuNaissance?->nom ?? '—' }}{{ $dignitaire->lieuNaissance?->pays ? ', ' . $dignitaire->lieuNaissance->pays->nom : '' }}</td>
            </tr>
            <tr>
                <td class="label">Nationalité</td>
                <td>{{ $dignitaire->nationalite ?? '—' }}</td>
                <td class="label">Adresse</td>
                <td>{{ $dignitaire->adresse ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Téléphone(s)</td>
                <td>{{ $dignitaire->telephones->pluck('numero')->join(', ') ?: ($dignitaire->telephone ?? '—') }}</td>
                <td class="label">Email(s)</td>
                <td>{{ $dignitaire->emails->pluck('email')->join(', ') ?: '—' }}</td>
            </tr>
        </table>
    </div>

    @if($dignitaire->postes->isNotEmpty())
    <div class="section">
        <div class="titre">Postes</div>
        <table class="data">
            <thead><tr><th>Intitulé</th><th>Entité</th><th>Ville</th><th>Début</th><th>Fin</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($dignitaire->postes as $poste)
                <tr>
                    <td>{{ $poste->intitule }}</td>
                    <td>{{ $poste->entite?->nom ?? '—' }}</td>
                    <td>{{ $poste->ville?->nom ?? '—' }}</td>
                    <td>{{ $poste->date_debut?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $poste->date_fin?->format('d/m/Y') ?? 'En cours' }}</td>
                    <td>{{ $poste->statut }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($dignitaire->nominations->isNotEmpty())
    <div class="section">
        <div class="titre">Nominations</div>
        <table class="data">
            <thead><tr><th>Fonction</th><th>Entité</th><th>Poste</th><th>Début</th><th>Fin</th><th>Statut</th></tr></thead>
            <tbody>
                @foreach($dignitaire->nominations as $nomination)
                <tr>
                    <td>{{ $nomination->fonction ?? '—' }}</td>
                    <td>{{ $nomination->entite?->nom ?? '—' }}</td>
                    <td>{{ $nomination->poste?->intitule ?? '—' }}</td>
                    <td>{{ $nomination->date_debut?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $nomination->date_fin?->format('d/m/Y') ?? 'En cours' }}</td>
                    <td>{{ $nomination->statut }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($dignitaire->decorations->isNotEmpty())
    <div class="section">
        <div class="titre">Décorations</div>
        <table class="data">
            <thead><tr><th>Nom</th><th>Type</th><th>Niveau</th><th>Grade</th><th>Date d'obtention</th></tr></thead>
            <tbody>
                @foreach($dignitaire->decorations as $decoration)
                <tr>
                    <td>{{ $decoration->nom }}</td>
                    <td>{{ $decoration->type ?? '—' }}</td>
                    <td>{{ $decoration->niveau ?? '—' }}</td>
                    <td>{{ $decoration->grade ?? '—' }}</td>
                    <td>{{ $decoration->date_obtention?->format('d/m/Y') ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($dignitaire->diplomes->isNotEmpty())
    <div class="section">
        <div class="titre">Diplômes</div>
        <table class="data">
            <thead><tr><th>Intitulé</th><th>Établissement</th><th>Domaine</th><th>Année</th></tr></thead>
            <tbody>
                @foreach($dignitaire->diplomes as $diplome)
                <tr>
                    <td>{{ $diplome->intitule }}</td>
                    <td>{{ $diplome->etablissement?->nom ?? '—' }}</td>
                    <td>{{ $diplome->domaine?->nom ?? '—' }}</td>
                    <td>{{ $diplome->annee ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($dignitaire->experiences->isNotEmpty())
    <div class="section">
        <div class="titre">Expériences professionnelles</div>
        <table class="data">
            <thead><tr><th>Intitulé</th><th>Structure</th><th>Début</th><th>Fin</th><th>Durée</th></tr></thead>
            <tbody>
                @foreach($dignitaire->experiences as $experience)
                <tr>
                    <td>{{ $experience->intitule }}</td>
                    <td>{{ $experience->structure?->nom ?? '—' }}</td>
                    <td>{{ $experience->date_debut?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $experience->date_fin?->format('d/m/Y') ?? 'En cours' }}</td>
                    <td>{{ $experience->duree_annees !== null ? $experience->duree_annees . ' an(s)' : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($dignitaire->languesParlees->isNotEmpty())
    <div class="section">
        <div class="titre">Langues parlées</div>
        <table class="data">
            <thead><tr><th>Langue</th><th>Niveau</th></tr></thead>
            <tbody>
                @foreach($dignitaire->languesParlees as $langue)
                <tr>
                    <td>{{ $langue->langue?->nom ?? '—' }}</td>
                    <td>{{ $langue->niveau ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($dignitaire->enfants->isNotEmpty())
    <div class="section">
        <div class="titre">Enfants</div>
        <table class="data">
            <thead><tr><th>Nom complet</th><th>Genre</th><th>Date de naissance</th><th>Âge</th><th>Lieu de naissance</th></tr></thead>
            <tbody>
                @foreach($dignitaire->enfants as $enfant)
                <tr>
                    <td>{{ $enfant->nom_complet }}</td>
                    <td>{{ $enfant->genre ?? '—' }}</td>
                    <td>{{ $enfant->date_naissance?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $enfant->age ?? '—' }}</td>
                    <td>{{ $enfant->lieuNaissance?->nom ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($dignitaire->conjoints->isNotEmpty())
    <div class="section">
        <div class="titre">Conjoint(s)</div>
        <table class="data">
            <thead><tr><th>Nom complet</th><th>Profession</th><th>Date de mariage</th><th>Statut</th><th>Contact</th></tr></thead>
            <tbody>
                @foreach($dignitaire->conjoints as $conjoint)
                <tr>
                    <td>{{ $conjoint->nom_complet }}</td>
                    <td>{{ $conjoint->profession ?? '—' }}</td>
                    <td>{{ $conjoint->date_mariage?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $conjoint->status_badge['text'] ?? $conjoint->statut }}</td>
                    <td>{{ $conjoint->telephone ?? $conjoint->email ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>
