<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Rapport {{ $type }}</title>
<style>
    @page { margin: 25px 25px 55px 25px; }
    body { font-family: Arial, Helvetica, sans-serif; color: #1f2937; font-size: 11px; }

    #footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 30px; font-size: 9px; color: #6b7280; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 6px; }
    #footer:after { content: "Page " counter(page) " / " counter(pages); }

    .entete { background: #16a34a; color: #ffffff; border-radius: 6px; padding: 16px 20px; margin-bottom: 16px; }
    .entete .titre { font-size: 18px; font-weight: bold; }
    .entete .sous { font-size: 11px; opacity: .9; margin-top: 4px; }

    .section { margin-bottom: 16px; }
    .section .titre { background-color: #eab308; color: #1f2937; font-weight: bold; padding: 5px 10px; font-size: 12px; margin-bottom: 6px; }

    table.kpi { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
    table.kpi td { padding: 8px 10px; border: 1px solid #e5e7eb; font-size: 11px; }
    table.kpi td.valeur { font-size: 16px; font-weight: bold; color: #16a34a; }

    table.data { width: 100%; border-collapse: collapse; }
    table.data th { background-color: #f3f4f6; text-align: left; padding: 5px 8px; font-size: 9.5px; border: 1px solid #d1d5db; }
    table.data td { padding: 5px 8px; border: 1px solid #e5e7eb; font-size: 9.5px; }
</style>
</head>
<body>
    <div id="footer">Gestion Dignitaires — République Gabonaise — Généré le {{ $genereLe }}</div>

    <div class="entete">
        <div class="titre">Rapport {{ ucfirst($type) }}</div>
        <div class="sous">Période du {{ $periodeDebut->format('d/m/Y') }} au {{ $periodeFin->format('d/m/Y') }}</div>
    </div>

    <div class="section">
        <div class="titre">Totaux globaux</div>
        <table class="kpi">
            <tr>
                <td>Dignitaires</td><td class="valeur">{{ $data['totaux']['totalDignitaires'] }}</td>
                <td>Postes</td><td class="valeur">{{ $data['totaux']['totalPostes'] }}</td>
                <td>Nominations</td><td class="valeur">{{ $data['totaux']['totalNominations'] }}</td>
            </tr>
            <tr>
                <td>Décorations</td><td class="valeur">{{ $data['totaux']['totalDecorations'] }}</td>
                <td>Diplômes</td><td class="valeur">{{ $data['totaux']['totalDiplomes'] }}</td>
                <td></td><td></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="titre">Activité sur la période</div>
        <table class="data">
            <thead><tr><th>Indicateur</th><th>Valeur</th></tr></thead>
            <tbody>
                <tr><td>Nominations créées</td><td>{{ $data['periode']['nominationsCreees'] }}</td></tr>
                <tr><td>Postes créés</td><td>{{ $data['periode']['postesCrees'] }}</td></tr>
                <tr><td>Décorations attribuées</td><td>{{ $data['periode']['decorationsAttribuees'] }}</td></tr>
                <tr><td>Diplômes obtenus</td><td>{{ $data['periode']['diplomesObtenus'] }}</td></tr>
                <tr><td>Candidatures validées</td><td>{{ $data['periode']['candidaturesValidees'] }}</td></tr>
                <tr><td>Candidatures refusées</td><td>{{ $data['periode']['candidaturesRefusees'] }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="titre">Répartition par genre</div>
        <table class="data">
            <thead><tr><th>Genre</th><th>Effectif</th></tr></thead>
            <tbody>
                <tr><td>Hommes</td><td>{{ $data['parGenre']['hommes'] }}</td></tr>
                <tr><td>Femmes</td><td>{{ $data['parGenre']['femmes'] }}</td></tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="titre">Répartition par statut</div>
        <table class="data">
            <thead><tr><th>Statut</th><th>Effectif</th></tr></thead>
            <tbody>
                @foreach($data['parStatut'] as $row)
                <tr><td>{{ $row->nom }}</td><td>{{ $row->count }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(count($data['parRegion']) > 0)
    <div class="section">
        <div class="titre">Top régions d'origine</div>
        <table class="data">
            <thead><tr><th>Région</th><th>Effectif</th></tr></thead>
            <tbody>
                @foreach($data['parRegion'] as $row)
                <tr><td>{{ $row->nom }}</td><td>{{ $row->count }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</body>
</html>
