<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dashboard_exposes_complete_metric_dictionary(): void
    {
        Sanctum::actingAs($this->administrator());

        $response = $this->getJson('/api/dashboard')->assertOk();
        $response->assertJsonStructure([
            'stats' => ['totalNominations', 'totalMilitaires'],
            'chartData' => [
                'parPaysAffectation',
                'parDomaine',
                'parLangue',
                'parNiveauAcademique',
            ],
            'derniersUtilisateurs',
            'dernieresNominations',
            'dernieresDecorations',
        ]);

        $this->assertSame(
            DB::table('nominations')->whereNull('deleted_at')->count(),
            $response->json('stats.totalNominations')
        );
        $this->assertSame(
            DB::table('dignitaire')->whereNull('deleted_at')->where('est_militaire', true)->count(),
            $response->json('stats.totalMilitaires')
        );
    }

    public function test_military_status_requires_grade_and_is_counted(): void
    {
        Sanctum::actingAs($this->administrator());
        $dignitaire = DB::table('dignitaire')->whereNull('deleted_at')->first();

        $payload = [
            'nip' => $dignitaire->nip,
            'matricule' => $dignitaire->matricule,
            'nom' => $dignitaire->nom,
            'prenom' => $dignitaire->prenom,
            'genre' => $dignitaire->genre,
            'etat_civil' => $dignitaire->etat_civil,
            'est_militaire' => true,
        ];
        $this->putJson("/api/dignitaires/{$dignitaire->id}", $payload)->assertUnprocessable()->assertJsonValidationErrors('grade_militaire');

        $payload['grade_militaire'] = 'Colonel';
        $this->putJson("/api/dignitaires/{$dignitaire->id}", $payload)->assertOk();
        $this->assertDatabaseHas('dignitaire', [
            'id' => $dignitaire->id,
            'est_militaire' => 1,
            'grade_militaire' => 'Colonel',
        ]);
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', ['Administrateur', 'Super Administrateur']))->first();
        $this->assertNotNull($user);
        return $user;
    }
}
