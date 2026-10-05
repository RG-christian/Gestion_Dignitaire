<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HistoricalArchiveTest extends TestCase
{
    use DatabaseTransactions;

    public function test_core_historical_resources_are_archived_and_restorable(): void
    {
        Sanctum::actingAs($this->administrator());

        foreach ([
            ['table' => 'nominations', 'uri' => 'nominations'],
            ['table' => 'affectations', 'uri' => 'affectations'],
            ['table' => 'postes', 'uri' => 'postes'],
            ['table' => 'conjoints', 'uri' => 'conjoints'],
        ] as $resource) {
            $id = DB::table($resource['table'])->whereNull('deleted_at')->value('id');
            $this->assertNotNull($id, "Une ligne {$resource['table']} est requise.");

            $this->deleteJson("/api/{$resource['uri']}/{$id}")->assertOk();
            $this->assertDatabaseHas($resource['table'], ['id' => $id]);
            $this->assertNotNull(DB::table($resource['table'])->where('id', $id)->value('deleted_at'));

            $this->getJson('/api/admin/archives')
                ->assertOk()
                ->assertJsonFragment(['id' => $id]);

            $this->postJson("/api/{$resource['uri']}/{$id}/restaurer")->assertOk();
            $this->assertNull(DB::table($resource['table'])->where('id', $id)->value('deleted_at'));
        }
    }

    public function test_dignitaire_is_archived_without_losing_related_history(): void
    {
        Sanctum::actingAs($this->administrator());
        $id = DB::table('dignitaire')->whereNull('deleted_at')->value('id');
        $relatedBefore = DB::table('postes')->where('dignitaire_id', $id)->count();

        $this->deleteJson("/api/dignitaires/{$id}")->assertOk();
        $this->assertSoftDeleted('dignitaire', ['id' => $id]);
        $this->assertSame($relatedBefore, DB::table('postes')->where('dignitaire_id', $id)->count());

        $this->postJson("/api/dignitaires/{$id}/restaurer")->assertOk();
        $this->assertDatabaseHas('dignitaire', ['id' => $id, 'deleted_at' => null]);
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', ['Administrateur', 'Super Administrateur']))->first();
        $this->assertNotNull($user);
        return $user;
    }
}
