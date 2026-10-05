<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PvManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_pv_routes_require_authentication(): void
    {
        $this->getJson('/api/pvs')->assertUnauthorized();
    }

    public function test_administrator_can_manage_and_archive_a_pv(): void
    {
        Sanctum::actingAs($this->administrator());

        $numero = 'PV-TEST-' . uniqid();
        $created = $this->postJson('/api/pvs', [
            'numero' => $numero,
            'date' => '2026-10-02',
            'description' => 'PV créé par le test transactionnel',
        ])->assertCreated()->assertJsonPath('numero', $numero);

        $id = $created->json('id');

        $this->getJson("/api/pvs/{$id}")
            ->assertOk()
            ->assertJsonPath('nominations_count', 0);

        $this->putJson("/api/pvs/{$id}", [
            'numero' => $numero,
            'date' => '2026-10-03',
            'description' => 'PV modifié',
        ])->assertOk()->assertJsonPath('description', 'PV modifié');

        $this->postJson("/api/pvs/{$id}/archiver")
            ->assertOk()
            ->assertJsonPath('statut', 'archive');

        $this->postJson("/api/pvs/{$id}/restaurer")
            ->assertOk()
            ->assertJsonPath('statut', 'actif');

        $this->deleteJson("/api/pvs/{$id}")->assertOk();
        $this->assertDatabaseMissing('pv', ['id' => $id]);
    }

    public function test_linked_pv_cannot_be_deleted(): void
    {
        Sanctum::actingAs($this->administrator());

        $pvId = DB::table('pv')->insertGetId([
            'numero' => 'PV-LIE-' . uniqid(),
            'date' => '2026-10-02',
            'statut' => 'actif',
        ]);

        $nominationId = DB::table('nominations')->value('id');
        $this->assertNotNull($nominationId, 'Une nomination de référence est requise pour ce test.');
        DB::table('nominations')->where('id', $nominationId)->update(['pv_id' => $pvId]);

        $this->deleteJson("/api/pvs/{$pvId}")
            ->assertStatus(409)
            ->assertJsonPath('message', 'Ce procès-verbal est lié à une nomination. Archivez-le au lieu de le supprimer.');

        $this->assertDatabaseHas('pv', ['id' => $pvId]);
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', [
            'Administrateur',
            'Super Administrateur',
        ]))->first();

        $this->assertNotNull($user, 'Un administrateur est requis pour tester les permissions PV.');

        return $user;
    }
}
