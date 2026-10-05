<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CandidateDocumentTransferTest extends TestCase
{
    use DatabaseTransactions;

    public function test_only_validated_documents_are_linked_without_copying_files(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($this->administrator());

        $candidat = DB::table('candidats')->where('statut', 'en_attente')->first();
        $this->assertNotNull($candidat);

        $validatedPath = "candidats/documents/transfer-{$candidat->id}.pdf";
        $pendingPath = "candidats/documents/pending-{$candidat->id}.pdf";
        $photoPath = "candidats/photos/transfer-{$candidat->id}.jpg";
        Storage::disk('public')->put($validatedPath, 'validated');
        Storage::disk('public')->put($pendingPath, 'pending');
        Storage::disk('public')->put($photoPath, 'photo');
        DB::table('candidats')->where('id', $candidat->id)->update(['photo' => $photoPath]);

        $validatedId = DB::table('candidat_documents')->insertGetId([
            'candidat_id' => $candidat->id,
            'type_document' => 'cv',
            'nom_fichier' => 'cv-valide.pdf',
            'chemin_fichier' => $validatedPath,
            'taille_fichier' => 9,
            'extension' => 'pdf',
            'description' => 'CV validé',
            'statut_validation' => 'valide',
            'uploaded_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('candidat_documents')->insert([
            'candidat_id' => $candidat->id,
            'type_document' => 'attestation',
            'nom_fichier' => 'attente.pdf',
            'chemin_fichier' => $pendingPath,
            'taille_fichier' => 7,
            'extension' => 'pdf',
            'statut_validation' => 'en_attente',
            'uploaded_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson("/api/admin/candidats/{$candidat->id}/valider");
        $response->assertOk()->assertJsonPath('success', true);
        $dignitaireId = $response->json('dignitaire.id');

        $this->assertDatabaseHas('dignitaire_documents', [
            'dignitaire_id' => $dignitaireId,
            'source_candidat_document_id' => $validatedId,
            'chemin_fichier' => $validatedPath,
        ]);
        $this->assertDatabaseMissing('dignitaire_documents', ['chemin_fichier' => $pendingPath]);
        Storage::disk('public')->assertExists($validatedPath);
        $this->assertCount(2, Storage::disk('public')->allFiles('candidats/documents'));

        $this->deleteJson("/api/admin/candidats/{$candidat->id}")->assertOk();
        $this->assertDatabaseHas('dignitaire_documents', [
            'dignitaire_id' => $dignitaireId,
            'source_candidat_document_id' => null,
            'chemin_fichier' => $validatedPath,
        ]);
        Storage::disk('public')->assertExists($validatedPath);
        Storage::disk('public')->assertExists($photoPath);
    }

    public function test_shared_file_survives_deletion_of_either_reference(): void
    {
        Storage::fake('public');
        $path = 'candidats/documents/shared.pdf';
        Storage::disk('public')->put($path, 'shared');

        $candidateDocument = \App\Models\CandidatDocument::query()->firstOrFail();
        $candidateDocument->update(['chemin_fichier' => $path]);
        $dignitaireId = DB::table('dignitaire')->value('id');
        $dignitaireDocument = \App\Models\DignitaireDocument::create([
            'dignitaire_id' => $dignitaireId,
            'source_candidat_document_id' => $candidateDocument->id,
            'type_document' => $candidateDocument->type_document,
            'nom_fichier' => 'shared.pdf',
            'chemin_fichier' => $path,
            'extension' => 'pdf',
        ]);

        $candidateDocument->delete();
        Storage::disk('public')->assertExists($path);
        $dignitaireDocument->refresh()->delete();
        Storage::disk('public')->assertMissing($path);
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', ['Administrateur', 'Super Administrateur']))->first();
        $this->assertNotNull($user);
        return $user;
    }
}
