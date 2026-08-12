<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Diffuse un email à tous les Administrateurs/Super Administrateurs pour un
 * événement de candidature (soumission, validation, refus) — désactivable
 * en un seul endroit via Parametres::NOTIF_ADMIN_CANDIDATURE (cf.
 * admin/parametres.vue), pour éviter de spammer les admins si le volume de
 * candidatures devient important.
 */
class AdminMailer
{
    public static function notifierCandidature(Mailable $mailable): void
    {
        if (!Parametres::getBool(Parametres::NOTIF_ADMIN_CANDIDATURE)) {
            return;
        }

        $admins = User::whereHas('role', function ($q) {
            $q->whereIn('role_name', ['Administrateur', 'Super Administrateur']);
        })->get();

        foreach ($admins as $admin) {
            if (!$admin->email) {
                continue;
            }

            try {
                Mail::to($admin->email)->send(clone $mailable);
            } catch (\Exception $e) {
                Log::warning('AdminMailer: échec envoi email', [
                    'admin' => $admin->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
