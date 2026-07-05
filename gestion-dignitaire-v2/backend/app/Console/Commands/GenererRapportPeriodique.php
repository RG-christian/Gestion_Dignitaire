<?php

namespace App\Console\Commands;

use App\Mail\RapportPeriodiqueGenere;
use App\Models\Rapport;
use App\Models\User;
use App\Support\Reports\SynthesisReportBuilder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenererRapportPeriodique extends Command
{
    protected $signature = 'rapports:generer {--periode=mensuel : mensuel|trimestriel|annuel}';

    protected $description = "Génère le rapport de synthèse périodique (PDF), l'archive et l'envoie par email aux administrateurs";

    public function handle(SynthesisReportBuilder $builder): int
    {
        $type = $this->option('periode');

        if (!in_array($type, ['mensuel', 'trimestriel', 'annuel'], true)) {
            $this->error("Période invalide : {$type}. Attendu : mensuel, trimestriel ou annuel.");
            return self::FAILURE;
        }

        [$debut, $fin] = match ($type) {
            'mensuel' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'trimestriel' => [now()->subQuarter()->firstOfQuarter(), now()->subQuarter()->lastOfQuarter()],
            'annuel' => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
        };

        $data = $builder->buildData($debut, $fin);

        $pdf = Pdf::loadView('exports.pdf.rapport-synthese', [
            'type' => $type,
            'periodeDebut' => $debut,
            'periodeFin' => $fin,
            'genereLe' => now()->format('d/m/Y à H:i'),
            'data' => $data,
        ])->setPaper('a4', 'portrait');

        $nomFichier = "rapport-{$type}-{$debut->format('Y-m-d')}.pdf";
        $cheminFichier = "rapports/{$type}/{$nomFichier}";
        Storage::put($cheminFichier, $pdf->output());

        $rapport = Rapport::create([
            'type' => $type,
            'periode_debut' => $debut->toDateString(),
            'periode_fin' => $fin->toDateString(),
            'nom_fichier' => $nomFichier,
            'chemin_fichier' => $cheminFichier,
            'taille_octets' => Storage::size($cheminFichier),
            'genere_le' => now(),
        ]);

        $admins = User::whereHas('role', fn ($q) => $q->whereIn('role_name', ['Administrateur', 'Super Administrateur']))->get();

        $envoyes = 0;
        $ignores = 0;

        foreach ($admins as $admin) {
            if (!$admin->email) {
                $ignores++;
                continue;
            }

            try {
                Mail::to($admin->email)->send(new RapportPeriodiqueGenere($rapport));
                $envoyes++;
            } catch (\Exception $e) {
                Log::warning('Echec envoi email de rapport périodique', [
                    'rapport_id' => $rapport->id,
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage(),
                ]);
                $ignores++;
            }
        }

        $this->info("Rapport {$type} généré ({$rapport->chemin_fichier}). Emails envoyés : {$envoyes}. Ignorés : {$ignores}.");

        return self::SUCCESS;
    }
}
