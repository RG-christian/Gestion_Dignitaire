<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DecorationAttributionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_routes_require_authentication(): void
    {
        $this->getJson('/api/decoration-attributions')->assertUnauthorized();
        $this->getJson('/api/decoration-attributions/initial-data')->assertUnauthorized();
    }

    public function test_administrator_manages_private_attestation(): void
    {
        Storage::fake('local');
        Sanctum::actingAs($this->administrator());
        $poste = DB::table('postes')->first();
        $decorationId = DB::table('decoration')->value('deco_id');

        $created = $this->post('/api/decoration-attributions', [
            'dignitaire_id' => $poste->dignitaire_id,
            'decoration_id' => $decorationId,
            'poste_id' => $poste->id,
            'date_attribution' => '2026-10-04',
            'attestation' => UploadedFile::fake()->createWithContent('attestation.pdf', '%PDF-1.4 test'),
        ])->assertCreated()->assertJsonPath('poste_id', $poste->id);

        $id = $created->json('id');
        $path = $created->json('attestation_path');
        Storage::disk('local')->assertExists($path);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $created->json('attestation_sha256'));

        $this->get("/api/decoration-attributions/{$id}/attestation")
            ->assertOk()->assertHeader('content-type', 'application/pdf');

        $updated = $this->post("/api/decoration-attributions/{$id}", [
            '_method' => 'PUT',
            'dignitaire_id' => $poste->dignitaire_id,
            'decoration_id' => $decorationId,
            'poste_id' => $poste->id,
            'date_attribution' => '2026-10-05',
            'attestation' => UploadedFile::fake()->createWithContent('nouvelle.pdf', '%PDF-1.4 replacement'),
        ])->assertOk();

        Storage::disk('local')->assertMissing($path);
        Storage::disk('local')->assertExists($updated->json('attestation_path'));
        $this->deleteJson("/api/decoration-attributions/{$id}")->assertOk();
        Storage::disk('local')->assertExists($updated->json('attestation_path'));
        $this->assertSoftDeleted('decoration_dignitaire', ['id' => $id]);
        $this->postJson("/api/decoration-attributions/{$id}/restaurer")->assertOk();
        $this->assertDatabaseHas('decoration_dignitaire', ['id' => $id, 'deleted_at' => null]);
    }

    public function test_poste_must_belong_to_selected_dignitaire_and_file_must_be_pdf(): void
    {
        Storage::fake('local');
        Sanctum::actingAs($this->administrator());
        $postes = DB::table('postes')->get();
        $poste = $postes->first();
        $otherDignitaire = DB::table('dignitaire')->where('id', '<>', $poste->dignitaire_id)->value('id');

        $this->post('/api/decoration-attributions', [
            'dignitaire_id' => $otherDignitaire,
            'decoration_id' => DB::table('decoration')->value('deco_id'),
            'poste_id' => $poste->id,
            'date_attribution' => '2026-10-04',
            'attestation' => UploadedFile::fake()->image('attestation.jpg'),
        ])->assertSessionHasErrors(['poste_id', 'attestation']);
    }

    public function test_attributed_decoration_cannot_be_deleted(): void
    {
        Sanctum::actingAs($this->administrator());
        $decorationId = DB::table('decoration_dignitaire')->value('decoration_id');
        $this->deleteJson("/api/decorations/{$decorationId}")->assertStatus(409);
        $this->assertDatabaseHas('decoration', ['deco_id' => $decorationId]);
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', ['Administrateur', 'Super Administrateur']))->first();
        $this->assertNotNull($user);
        return $user;
    }
}
