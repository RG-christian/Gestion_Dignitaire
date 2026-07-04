<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Point d'entrée unique pour journaliser une action (création/modification/
 * suppression/validation/refus) sur une entité métier. Utilisé aussi bien
 * dans les contrôleurs Eloquent que dans ceux basés sur Query Builder.
 */
class AuditLogger
{
    public static function log(
        Request $request,
        string $action,
        string $auditableType,
        ?int $auditableId,
        ?string $auditableLabel = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            $causer = $request->user();

            AuditLog::create([
                'causer_type' => $causer ? get_class($causer) : null,
                'causer_id' => $causer?->id,
                'causer_label' => $causer?->username ?? $causer?->nom_complet ?? $causer?->email ?? null,
                'action' => $action,
                'auditable_type' => $auditableType,
                'auditable_id' => $auditableId,
                'auditable_label' => $auditableLabel,
                'old_values' => $oldValues,
                'new_values' => $newValues,
            ]);
        } catch (\Exception $e) {
            // Un échec de journalisation ne doit jamais faire échouer l'action métier
            Log::warning('AuditLogger: échec de journalisation', [
                'action' => $action,
                'auditable_type' => $auditableType,
                'auditable_id' => $auditableId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
