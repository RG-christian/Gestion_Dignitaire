<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>{{ $title }}</title>
<style>
    @page { margin: 90px 25px 60px 25px; }
    body { font-family: Arial, Helvetica, sans-serif; color: #1f2937; font-size: 11px; }

    #header { position: fixed; top: -70px; left: 0; right: 0; height: 70px; }
    #header .bandeau {
        background-color: #16a34a;
        color: #ffffff;
        padding: 12px 16px;
        border-radius: 6px;
    }
    #header .titre { font-size: 16px; font-weight: bold; }
    #header .sous-titre { font-size: 10px; opacity: .9; margin-top: 2px; }

    #footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 30px; font-size: 9px; color: #6b7280; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 6px; }
    #footer:after { content: "Page " counter(page) " / " counter(pages); }

    .filtres { font-size: 10px; color: #4b5563; margin-bottom: 10px; font-style: italic; }

    table.data { width: 100%; border-collapse: collapse; }
    table.data th {
        background-color: #eab308;
        color: #1f2937;
        text-align: left;
        padding: 6px 8px;
        font-size: 10px;
        border: 1px solid #d1d5db;
    }
    table.data td {
        padding: 5px 8px;
        border: 1px solid #e5e7eb;
        font-size: 10px;
    }
    table.data tr:nth-child(even) td { background-color: #f9fafb; }
</style>
</head>
<body>
    <div id="header">
        <div class="bandeau">
            <div class="titre">{{ $title }}</div>
            <div class="sous-titre">Gestion Dignitaires — République Gabonaise — Généré le {{ $genereLe }}</div>
        </div>
    </div>

    <div id="footer"></div>

    @if($filtresResume)
        <div class="filtres">Filtres appliqués : {{ $filtresResume }}</div>
    @endif

    <table class="data">
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headings) }}" style="text-align:center; color:#6b7280;">Aucune donnée pour ces filtres.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
