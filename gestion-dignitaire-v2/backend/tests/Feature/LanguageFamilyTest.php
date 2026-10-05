<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LanguageFamilyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_language_family_can_be_created_updated_displayed_and_searched(): void
    {
        Sanctum::actingAs($this->administrator());

        $created = $this->postJson('/api/langues', [
            'nom' => 'Langue recette ' . uniqid(),
            'code_iso' => 'tst',
            'famille' => 'Famille de recette',
            'nb_locuteurs' => '100',
        ])->assertCreated()
            ->assertJsonPath('famille', 'Famille de recette');
        $id = $created->json('id');

        $this->getJson('/api/langues?search=' . urlencode('Famille de recette'))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $id)
            ->assertJsonPath('0.famille', 'Famille de recette');

        $this->putJson("/api/langues/{$id}", [
            'nom' => $created->json('nom'),
            'code_iso' => 'tst',
            'famille' => 'Famille actualisée',
            'nb_locuteurs' => '100',
        ])->assertOk()
            ->assertJsonPath('famille', 'Famille actualisée');

        $this->getJson("/api/langues/{$id}")
            ->assertOk()
            ->assertJsonPath('famille', 'Famille actualisée');
    }

    public function test_language_family_is_optional_and_routes_require_authentication(): void
    {
        $this->getJson('/api/langues')->assertUnauthorized();
        $this->postJson('/api/langues', ['nom' => 'Interdit'])->assertUnauthorized();

        Sanctum::actingAs($this->administrator());
        $this->postJson('/api/langues', ['nom' => 'Sans famille ' . uniqid()])
            ->assertCreated()
            ->assertJsonPath('famille', null);
    }

    public function test_spoken_language_level_is_limited_to_the_four_business_values(): void
    {
        Sanctum::actingAs($this->administrator());
        $dignitaireId = DB::table('dignitaire')
            ->whereNotIn('id', DB::table('langues')->select('dignitaire_id'))
            ->value('id');
        $langueId = DB::table('langue')->value('id');
        $this->assertNotNull($dignitaireId);
        $this->assertNotNull($langueId);

        $this->postJson('/api/langues-parlees', [
            'dignitaire_id' => $dignitaireId,
            'langue_id' => $langueId,
            'niveau' => 'Moyen',
        ])->assertCreated()->assertJsonPath('niveau', 'Moyen');

        $this->postJson('/api/langues-parlees', [
            'dignitaire_id' => $dignitaireId,
            'langue_id' => $langueId,
            'niveau' => 'Intermédiaire',
        ])->assertUnprocessable()->assertJsonValidationErrors('niveau');
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', ['Administrateur', 'Super Administrateur']))->first();
        $this->assertNotNull($user);
        return $user;
    }
}
