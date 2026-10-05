<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AcademicReferenceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_administrator_can_manage_domains_and_duplicate_names_are_rejected(): void
    {
        Sanctum::actingAs($this->administrator());

        $created = $this->postJson('/api/domaines', ['nom' => 'Domaine de recette', 'description' => 'Description initiale'])
            ->assertCreated()
            ->assertJsonPath('nom', 'Domaine de recette');
        $id = $created->json('id');

        $this->postJson('/api/domaines', ['nom' => 'domaine DE RECETTE'])->assertUnprocessable();
        $this->putJson("/api/domaines/{$id}", ['nom' => 'Domaine actualisé', 'description' => 'Description finale'])
            ->assertOk()
            ->assertJsonPath('description', 'Description finale');
        $this->getJson("/api/domaines/{$id}")->assertOk()->assertJsonPath('nom', 'Domaine actualisé');
        $this->deleteJson("/api/domaines/{$id}")->assertOk();

        $this->assertDatabaseMissing('domaine', ['id' => $id]);
    }

    public function test_administrator_can_manage_establishments_and_find_or_create_avoids_duplicates(): void
    {
        Sanctum::actingAs($this->administrator());
        $villeId = DB::table('ville')->value('id');

        $created = $this->postJson('/api/etablissements', ['nom' => 'Établissement de recette', 'type' => 'Institut', 'ville_id' => $villeId])
            ->assertCreated()
            ->assertJsonPath('type', 'Institut');
        $id = $created->json('id');

        $this->postJson('/api/etablissements', ['nom' => 'ÉTABLISSEMENT DE RECETTE'])
            ->assertOk()
            ->assertJsonPath('id', $id);
        $this->putJson("/api/etablissements/{$id}", ['nom' => 'Établissement actualisé', 'type' => 'Université', 'ville_id' => $villeId])
            ->assertOk()
            ->assertJsonPath('type', 'Université')
            ->assertJsonPath('ville.id', $villeId);
        $this->getJson("/api/etablissements/{$id}")->assertOk()->assertJsonPath('nom', 'Établissement actualisé');
        $this->deleteJson("/api/etablissements/{$id}")->assertOk();

        $this->assertDatabaseMissing('etablissement', ['id' => $id]);
    }

    public function test_references_used_by_diplomas_cannot_be_deleted(): void
    {
        Sanctum::actingAs($this->administrator());
        $diplome = DB::table('diplome')->whereNotNull('domaine_id')->whereNotNull('etablissement_id')->first();
        $this->assertNotNull($diplome);

        $this->deleteJson("/api/domaines/{$diplome->domaine_id}")
            ->assertStatus(409)
            ->assertJsonPath('usages', DB::table('diplome')->where('domaine_id', $diplome->domaine_id)->count());
        $this->deleteJson("/api/etablissements/{$diplome->etablissement_id}")
            ->assertStatus(409)
            ->assertJsonPath('usages', DB::table('diplome')->where('etablissement_id', $diplome->etablissement_id)->count());

        $this->assertDatabaseHas('domaine', ['id' => $diplome->domaine_id]);
        $this->assertDatabaseHas('etablissement', ['id' => $diplome->etablissement_id]);
    }

    public function test_academic_reference_routes_require_authentication(): void
    {
        $this->getJson('/api/domaines')->assertUnauthorized();
        $this->postJson('/api/domaines', ['nom' => 'Interdit'])->assertUnauthorized();
        $this->getJson('/api/etablissements')->assertUnauthorized();
        $this->postJson('/api/etablissements', ['nom' => 'Interdit'])->assertUnauthorized();
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', ['Administrateur', 'Super Administrateur']))->first();
        $this->assertNotNull($user);
        return $user;
    }
}
