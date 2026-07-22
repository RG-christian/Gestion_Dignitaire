<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * MySQL `TIMESTAMP` columns sont converties selon le fuseau *session* du
 * serveur (`@@session.time_zone`), qui vaut `SYSTEM` ici (fuseau Windows
 * local) — alors que Laravel calcule `now()` en UTC (`config('app.timezone')`).
 * Sur cette machine l'écart est d'environ 1h, ce qui invalidait les calculs
 * d'expiration OTP (fenêtre de seulement 10 min) de façon aléatoire.
 * `DATETIME` n'a pas ce problème : la valeur est stockée et relue telle
 * quelle, sans conversion de fuseau.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE otp_codes MODIFY expires_at DATETIME NOT NULL');
        DB::statement('ALTER TABLE otp_codes MODIFY created_at DATETIME NULL');
        DB::statement('ALTER TABLE otp_codes MODIFY updated_at DATETIME NULL');

        DB::statement('ALTER TABLE password_reset_tokens MODIFY created_at DATETIME NULL');

        DB::statement('ALTER TABLE parametres MODIFY created_at DATETIME NULL');
        DB::statement('ALTER TABLE parametres MODIFY updated_at DATETIME NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE otp_codes MODIFY expires_at TIMESTAMP NOT NULL');
        DB::statement('ALTER TABLE otp_codes MODIFY created_at TIMESTAMP NULL');
        DB::statement('ALTER TABLE otp_codes MODIFY updated_at TIMESTAMP NULL');

        DB::statement('ALTER TABLE password_reset_tokens MODIFY created_at TIMESTAMP NULL');

        DB::statement('ALTER TABLE parametres MODIFY created_at TIMESTAMP NULL');
        DB::statement('ALTER TABLE parametres MODIFY updated_at TIMESTAMP NULL');
    }
};
