<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardIndicatorsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dashboard_exposes_required_totals_distributions_and_latest_items(): void
    {
        Sanctum::actingAs($this->administrator());
        $dignitaireId = DB::table('dignitaire')->whereNull('deleted_at')->value('id');
        DB::table('dignitaire')->where('id', $dignitaireId)->update([
            'est_militaire' => true,
            'grade_militaire' => 'Général de test',
        ]);

        $response = $this->getJson('/api/dashboard')->assertOk();

        $response
            ->assertJsonPath('stats.totalNominations', DB::table('nominations')->whereNull('deleted_at')->count())
            ->assertJsonPath('stats.totalMilitaires', 1)
            ->assertJsonStructure([
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

        $this->assertCount(
            DB::table('affectations')->whereNull('deleted_at')->whereNotNull('pays_id')->distinct()->pluck('pays_id')->count(),
            $response->json('chartData.parPaysAffectation')
        );
        $this->assertNotEmpty($response->json('chartData.parDomaine'));
        $this->assertNotEmpty($response->json('chartData.parLangue'));
        $this->assertNotEmpty($response->json('chartData.parNiveauAcademique'));
    }

    public function test_military_status_requires_a_grade_and_is_persisted(): void
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
            'statut' => $dignitaire->statut,
            'est_militaire' => true,
        ];

        $this->putJson("/api/dignitaires/{$dignitaire->id}", $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('grade_militaire');

        $this->putJson("/api/dignitaires/{$dignitaire->id}", [
            ...$payload,
            'grade_militaire' => 'Colonel',
        ])->assertOk()->assertJsonPath('est_militaire', true);

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
