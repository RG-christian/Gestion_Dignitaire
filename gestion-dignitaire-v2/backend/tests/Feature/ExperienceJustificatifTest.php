<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExperienceJustificatifTest extends TestCase
{
    use DatabaseTransactions;

    public function test_experience_routes_require_authentication(): void
    {
        $this->getJson('/api/experiences')->assertUnauthorized();
        $this->getJson('/api/experiences/1/justificatif')->assertUnauthorized();
    }

    public function test_administrator_can_upload_replace_download_and_delete_pdf(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->administrator());

        $dignitaireId = DB::table('dignitaire')->value('id');
        $this->assertNotNull($dignitaireId, 'Un dignitaire de référence est requis.');

        $created = $this->post('/api/experiences', [
            'dignitaire_id' => $dignitaireId,
            'intitule' => 'Expérience justificatif test',
            'date_debut' => '2020-01-01',
            'justificatif' => UploadedFile::fake()->create('preuve.pdf', 100, 'application/pdf'),
        ])->assertCreated()->assertJsonPath('intitule', 'Expérience justificatif test');

        $id = $created->json('id');
        $firstPath = $created->json('justificatif_path');
        $this->assertNotNull($firstPath);
        Storage::disk('public')->assertExists($firstPath);

        $this->get("/api/experiences/{$id}/justificatif")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $updated = $this->post("/api/experiences/{$id}", [
            '_method' => 'PUT',
            'dignitaire_id' => $dignitaireId,
            'intitule' => 'Expérience justificatif modifiée',
            'date_debut' => '2020-01-01',
            'justificatif' => UploadedFile::fake()->create('nouvelle-preuve.pdf', 120, 'application/pdf'),
        ])->assertOk();

        $secondPath = $updated->json('justificatif_path');
        $this->assertNotSame($firstPath, $secondPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);

        $this->deleteJson("/api/experiences/{$id}")->assertOk();
        Storage::disk('public')->assertMissing($secondPath);
        $this->assertDatabaseMissing('experiences', ['id' => $id]);
    }

    public function test_non_pdf_justificatif_is_rejected(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->administrator());

        $dignitaireId = DB::table('dignitaire')->value('id');

        $this->post('/api/experiences', [
            'dignitaire_id' => $dignitaireId,
            'intitule' => 'Fichier invalide',
            'justificatif' => UploadedFile::fake()->image('preuve.jpg'),
        ])->assertSessionHasErrors('justificatif');

        $this->assertDatabaseMissing('experiences', ['intitule' => 'Fichier invalide']);
    }

    public function test_missing_justificatif_returns_not_found(): void
    {
        Sanctum::actingAs($this->administrator());

        $experienceId = DB::table('experiences')->whereNull('justificatif_path')->value('id');
        $this->assertNotNull($experienceId, 'Une expérience sans justificatif est requise.');

        $this->getJson("/api/experiences/{$experienceId}/justificatif")
            ->assertNotFound()
            ->assertJsonPath('message', 'Aucun justificatif disponible pour cette expérience');
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', [
            'Administrateur',
            'Super Administrateur',
        ]))->first();

        $this->assertNotNull($user, 'Un administrateur est requis pour tester les expériences.');

        return $user;
    }
}
