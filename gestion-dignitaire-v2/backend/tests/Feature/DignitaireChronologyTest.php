<?php

namespace Tests\Feature;

use App\Models\Conjoint;
use App\Models\Dignitaire;
use App\Models\Diplome;
use App\Models\Enfant;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DignitaireChronologyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_family_timeline_orders_birth_union_and_end_of_union_without_losing_history(): void
    {
        Sanctum::actingAs($this->administrator());
        $dignitaire = $this->dignitaire();

        $conjoint = Conjoint::create([
            'dignitaire_id' => $dignitaire->id,
            'nom' => 'Chronologie',
            'prenom' => 'Conjoint',
            'genre' => 'F',
            'date_mariage' => '2005-06-10',
            'statut' => 'actif',
        ]);
        Enfant::create([
            'dignitaire_id' => $dignitaire->id,
            'nom' => 'Chronologie',
            'prenom' => 'Enfant',
            'genre' => 'M',
            'date_naissance' => '2010-03-12',
        ]);

        $conjoint->terminerUnion('divorce', new \DateTime('2020-01-15'));

        $response = $this->getJson("/api/dignitaires/{$dignitaire->id}/chronologie")
            ->assertOk()
            ->assertJsonPath('dignitaire.id', $dignitaire->id)
            ->assertJsonCount(3, 'familiale')
            ->assertJsonPath('familiale.0.type', 'fin_union')
            ->assertJsonPath('familiale.0.date', '2020-01-15')
            ->assertJsonPath('familiale.1.type', 'naissance')
            ->assertJsonPath('familiale.2.type', 'union');

        $this->assertSame('divorce', $response->json('familiale.0.statut'));
        $this->assertDatabaseHas('conjoints', ['id' => $conjoint->id, 'date_fin_union' => '2020-01-15 00:00:00']);
    }

    public function test_academic_and_professional_timelines_are_sorted_grouped_and_mark_current_experiences(): void
    {
        Sanctum::actingAs($this->administrator());
        $dignitaire = $this->dignitaire();

        Diplome::create(['dignitaire_id' => $dignitaire->id, 'intitule' => 'Licence test', 'annee' => '2015', 'type' => 'Licence']);
        Diplome::create(['dignitaire_id' => $dignitaire->id, 'intitule' => 'Master test', 'annee' => '2018', 'type' => 'Master']);
        Experience::create(['dignitaire_id' => $dignitaire->id, 'intitule' => 'Ancienne fonction', 'date_debut' => '2016-01-01', 'date_fin' => '2019-12-31']);
        Experience::create(['dignitaire_id' => $dignitaire->id, 'intitule' => 'Fonction actuelle', 'date_debut' => '2022-02-01', 'date_fin' => null]);

        $this->getJson("/api/dignitaires/{$dignitaire->id}/chronologie")
            ->assertOk()
            ->assertJsonCount(2, 'academique.elements')
            ->assertJsonPath('academique.elements.0.intitule', 'Master test')
            ->assertJsonPath('academique.groupes.Master.0.intitule', 'Master test')
            ->assertJsonPath('academique.groupes.Licence.0.intitule', 'Licence test')
            ->assertJsonCount(2, 'professionnelle')
            ->assertJsonPath('professionnelle.0.intitule', 'Fonction actuelle')
            ->assertJsonPath('professionnelle.0.en_cours', true)
            ->assertJsonPath('professionnelle.0.date_fin', null)
            ->assertJsonPath('professionnelle.1.en_cours', false);
    }

    public function test_chronology_requires_authentication(): void
    {
        $dignitaire = $this->dignitaire();
        $this->getJson("/api/dignitaires/{$dignitaire->id}/chronologie")->assertUnauthorized();
    }

    private function dignitaire(): Dignitaire
    {
        return Dignitaire::create([
            'matricule' => 'CHRONO-' . uniqid(),
            'nom' => 'Test',
            'prenom' => 'Chronologie',
            'genre' => 'Homme',
            'statut' => 'actif',
        ]);
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', ['Administrateur', 'Super Administrateur']))->first();
        $this->assertNotNull($user);
        return $user;
    }
}
