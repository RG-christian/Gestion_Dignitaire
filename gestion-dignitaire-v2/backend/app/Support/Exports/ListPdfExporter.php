<?php

namespace App\Support\Exports;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfInstance;

/**
 * Rendu PDF générique pour les exports de liste : un seul gabarit
 * (exports.pdf.generic-list) paramétré par titre/colonnes/lignes,
 * réutilisé par tous les modules.
 */
class ListPdfExporter
{
    public function render(string $title, array $headings, iterable $rows, ?string $filtresResume = null): PdfInstance
    {
        return Pdf::loadView('exports.pdf.generic-list', [
            'title' => $title,
            'headings' => $headings,
            'rows' => $rows,
            'filtresResume' => $filtresResume,
            'genereLe' => now()->format('d/m/Y à H:i'),
        ])->setPaper('a4', 'landscape');
    }
}
