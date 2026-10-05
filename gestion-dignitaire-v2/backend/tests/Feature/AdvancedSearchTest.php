<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdvancedSearchTest extends TestCase
{
    use DatabaseTransactions;

    public function test_filters_are_combined_and_results_are_paginated(): void
    {
        Sanctum::actingAs($this->administrator());
        $dignitaireId = DB::table('affectations')->whereNull('deleted_at')->value('dignitaire_id');
        $this->assertNotNull($dignitaireId);

        $dignitaire = DB::table('dignitaire')->where('id', $dignitaireId)->first();
        $filters = [
            'q' => $dignitaire->matricule,
            'poste' => DB::table('postes')->where('dignitaire_id', $dignitaireId)->whereNull('deleted_at')->value('intitule'),
            'pays_affectation_id' => DB::table('affectations')->where('dignitaire_id', $dignitaireId)->whereNull('deleted_at')->value('pays_id'),
            'langue_id' => DB::table('langues')->where('dignitaire_id', $dignitaireId)->value('langue_id'),
            'domaine_id' => DB::table('diplome')->where('dignitaire_id', $dignitaireId)->value('domaine_id'),
            'niveau_academique' => DB::table('diplome')->where('dignitaire_id', $dignitaireId)->value('type'),
            'structure_id' => DB::table('experiences')->where('dignitaire_id', $dignitaireId)->value('structure_id'),
            'per_page' => 10,
        ];

        foreach ($filters as $name => $value) {
            if ($name !== 'per_page') {
                $this->assertNotNull($value, "Le jeu de recette doit fournir le critère {$name}.");
            }
        }

        $response = $this->getJson('/api/search/advanced?' . http_build_query($filters))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('results.per_page', 10)
            ->assertJsonPath('results.total', 1)
            ->assertJsonPath('results.data.0.id', $dignitaireId);

        $this->assertSame($dignitaire->matricule, $response->json('results.data.0.matricule'));
    }

    public function test_boolean_filters_and_options_are_available(): void
    {
        Sanctum::actingAs($this->administrator());
        $id = DB::table('dignitaire')->whereNull('deleted_at')->value('id');
        DB::table('dignitaire')->where('id', $id)->update(['est_militaire' => true, 'grade_militaire' => 'Test']);

        $this->getJson('/api/search/advanced?est_militaire=1')
            ->assertOk()
            ->assertJsonPath('results.total', 1)
            ->assertJsonPath('results.data.0.id', $id);

        $this->getJson('/api/search/advanced/options')
            ->assertOk()
            ->assertJsonStructure(['postes', 'pays', 'langues', 'domaines', 'niveaux_academiques', 'structures']);
    }

    public function test_routes_require_authentication(): void
    {
        $this->getJson('/api/search/advanced')->assertUnauthorized();
        $this->getJson('/api/search/advanced/options')->assertUnauthorized();
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', ['Administrateur', 'Super Administrateur']))->first();
        $this->assertNotNull($user);
        return $user;
    }
}
