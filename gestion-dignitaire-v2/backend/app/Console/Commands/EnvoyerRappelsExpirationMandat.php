<?php

namespace App\Console\Commands;

use App\Mail\MandatExpirationProche;
use App\Models\Nomination;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnvoyerRappelsExpirationMandat extends Command
{
    protected $signature = 'mandats:rappels {--jours=30 : Nombre de jours avant échéance à partir duquel alerter}';

    protected $description = "Envoie un email de rappel aux dignitaires dont le mandat (nomination) arrive à échéance";

    public function handle(): int
    {
        $joursAvance = (int) $this->option('jours');

        $nominations = Nomination::with(['dignitaire'])
            ->where('statut', 'en_cours')
            ->where('rappel_envoye', false)
            ->whereNotNull('date_fin')
            ->whereBetween('date_fin', [now()->startOfDay(), now()->addDays($joursAvance)->endOfDay()])
            ->get();

        $envoyes = 0;
        $ignores = 0;

        foreach ($nominations as $nomination) {
            $dignitaire = $nomination->dignitaire;

            if (!$dignitaire) {
                $ignores++;
                continue;
            }

            $email = $dignitaire->emailNotification();

            if (!$email) {
                $ignores++;
                continue;
            }

            try {
                Mail::to($email)->send(new MandatExpirationProche($dignitaire, $nomination));
                $nomination->update(['rappel_envoye' => true]);
                $envoyes++;
            } catch (\Exception $e) {
                Log::warning('Echec envoi rappel expiration mandat', [
                    'nomination_id' => $nomination->id,
                    'error' => $e->getMessage(),
                ]);
                $ignores++;
            }
        }

        $this->info("Rappels envoyés : {$envoyes}. Ignorés (pas d'email ou échec) : {$ignores}.");

        return self::SUCCESS;
    }
}
