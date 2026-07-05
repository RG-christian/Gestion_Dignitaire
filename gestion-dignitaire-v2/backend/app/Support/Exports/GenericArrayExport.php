<?php

namespace App\Support\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Export Excel générique : une seule classe réutilisée par tous les modules
 * (dignitaires, nominations, décorations, ...), qui reçoit déjà les
 * en-têtes et les lignes prêtes à l'emploi (tableaux de scalaires).
 */
class GenericArrayExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(
        private array $headings,
        private Collection $rows,
        private string $title = 'Export',
    ) {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }
}
