<?php

namespace App\Support\Imports;

use App\Models\Dignitaire;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Import Excel des dignitaires (CR de réunion : "Import possible depuis des
 * fichiers Excel pour éviter la saisie manuelle"). Chaque ligne est validée
 * et créée indépendamment ; les lignes en erreur sont collectées sans
 * bloquer l'import des lignes valides. Le matricule, quand absent, est
 * auto-généré comme pour la validation d'un candidat (voir CandidatController).
 */
class DignitaireImport implements ToCollection, WithHeadingRow
{
    public array $crees = [];
    public array $erreurs = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $ligne = $index + 2; // +1 pour l'en-tête, +1 pour l'index 0-based

            $data = [
                'nom' => trim((string) ($row['nom'] ?? '')),
                'prenom' => trim((string) ($row['prenom'] ?? '')),
                'nip' => $this->valeurOuNull($row['nip'] ?? null),
                'matricule' => $this->valeurOuNull($row['matricule'] ?? null),
                'date_naissance' => $this->parseDate($row['date_naissance'] ?? null),
                'genre' => $this->valeurOuNull($row['genre'] ?? null),
                'etat_civil' => $this->valeurOuNull($row['etat_civil'] ?? null),
                'nationalite' => $this->valeurOuNull($row['nationalite'] ?? null),
                'telephone' => $this->valeurOuNull($row['telephone'] ?? null),
                'adresse' => $this->valeurOuNull($row['adresse'] ?? null),
                'statut' => $this->valeurOuNull($row['statut'] ?? null) ?? 'actif',
            ];

            if ($data['nom'] === '' && $data['prenom'] === '') {
                continue; // ligne vide, ignorée silencieusement
            }

            $validator = Validator::make($data, [
                'nom' => 'required|string|max:100',
                'prenom' => 'required|string|max:100',
                'nip' => 'nullable|string|max:20|unique:dignitaire,nip',
                'matricule' => 'nullable|string|max:20|unique:dignitaire,matricule',
                'date_naissance' => 'nullable|date',
                'genre' => 'nullable|in:Homme,Femme',
                'statut' => 'nullable|in:actif,retraite,non_localise',
            ]);

            if ($validator->fails()) {
                $this->erreurs[] = [
                    'ligne' => $ligne,
                    'nom' => $data['nom'],
                    'prenom' => $data['prenom'],
                    'messages' => $validator->errors()->all(),
                ];
                continue;
            }

            $valide = $validator->validated();
            $matriculeFourni = $valide['matricule'] ?? null;
            unset($valide['matricule']);

            // Matricule temporaire unique pour passer la contrainte NOT NULL,
            // remplacé juste après par un matricule provisoire basé sur l'id
            // si aucun matricule n'a été fourni dans le fichier.
            $dignitaire = Dignitaire::create($valide + [
                'matricule' => $matriculeFourni ?? ('TMP' . uniqid()),
            ]);

            if (!$matriculeFourni) {
                $dignitaire->update(['matricule' => 'PROV' . str_pad((string) $dignitaire->id, 6, '0', STR_PAD_LEFT)]);
            }

            $this->crees[] = $dignitaire;
        }
    }

    private function valeurOuNull(mixed $valeur): ?string
    {
        $valeur = is_string($valeur) ? trim($valeur) : $valeur;

        return ($valeur === '' || $valeur === null) ? null : (string) $valeur;
    }

    private function parseDate(mixed $valeur): ?string
    {
        if (empty($valeur)) {
            return null;
        }

        if (is_numeric($valeur)) {
            // Date sérialisée Excel
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valeur)->format('Y-m-d');
        }

        try {
            return \Illuminate\Support\Carbon::parse((string) $valeur)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }
}
