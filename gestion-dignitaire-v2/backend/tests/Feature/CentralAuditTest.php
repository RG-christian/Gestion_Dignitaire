<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Http\Middleware\AuditMutatingRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CentralAuditTest extends TestCase
{
    use DatabaseTransactions;

    public function test_successful_uninstrumented_write_gets_automatic_audit_context(): void
    {
        $user = $this->administrator();
        Sanctum::actingAs($user);

        $this->withHeader('User-Agent', 'CentralAuditTest/1.0')
            ->putJson('/api/profile', [
                'nom_complet' => $user->nom_complet,
                'email' => $user->email,
                'telephone' => $user->telephone,
            ])
            ->assertOk();

        $log = AuditLog::where('request_path', '/api/profile')->latest('id')->first();
        $this->assertNotNull($log);
        $this->assertSame('updated', $log->action);
        $this->assertSame('Profile', $log->auditable_type);
        $this->assertSame('PUT', $log->http_method);
        $this->assertSame(200, $log->response_status);
        $this->assertNotNull($log->request_id);
        $this->assertSame('CentralAuditTest/1.0', $log->user_agent);
        $this->assertSame(['journalisation' => 'automatique'], $log->new_values);
    }

    public function test_detailed_controller_log_is_enriched_without_duplicate_fallback(): void
    {
        Sanctum::actingAs($this->administrator());
        $name = 'Langue audit ' . uniqid();

        $this->postJson('/api/langues', ['nom' => $name])
            ->assertCreated();

        $log = AuditLog::where('auditable_type', 'Langue')
            ->where('auditable_label', $name)
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertNotNull($log->request_id);
        $this->assertSame('POST', $log->http_method);
        $this->assertSame('/api/langues', $log->request_path);
        $this->assertSame(201, $log->response_status);
        $this->assertSame(1, AuditLog::where('request_id', $log->request_id)->count());
    }

    public function test_failed_write_is_not_recorded_as_success(): void
    {
        $before = AuditLog::count();

        $this->putJson('/api/profile', [])->assertUnauthorized();

        $this->assertSame($before, AuditLog::count());
    }

    public function test_error_response_rolls_back_business_writes(): void
    {
        $marker = 'rollback-' . uniqid();
        $request = Request::create('/api/test-audit-rollback', 'POST');

        $response = (new AuditMutatingRequest())->handle($request, function () use ($marker) {
            DB::table('audit_logs')->insert([
                'action' => 'updated',
                'auditable_type' => 'RollbackProbe',
                'auditable_label' => $marker,
                'created_at' => now(),
            ]);

            return response()->json(['message' => 'échec simulé'], 500);
        });

        $this->assertSame(500, $response->getStatusCode());
        $this->assertDatabaseMissing('audit_logs', ['auditable_label' => $marker]);
    }

    private function administrator(): User
    {
        $user = User::whereHas('role', fn ($query) => $query->whereIn('role_name', ['Administrateur', 'Super Administrateur']))->first();
        $this->assertNotNull($user);
        return $user;
    }
}
