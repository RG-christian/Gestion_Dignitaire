<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Http\Request;

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
        ?array $newValues = null,
        mixed $causerOverride = null
    ): void {
        $causer = $causerOverride ?? $request->user();

        AuditLog::create([
                'request_id' => $request->attributes->get('audit_request_id'),
                'causer_type' => $causer ? get_class($causer) : null,
                'causer_id' => $causer?->id,
                'causer_label' => $causer?->username
                    ?? $causer?->nom_complet
                    ?? (isset($causer->prenom) ? trim($causer->prenom . ' ' . ($causer->nom ?? '')) : null)
                    ?? $causer?->email
                    ?? null,
                'action' => $action,
                'auditable_type' => $auditableType,
                'auditable_id' => $auditableId,
                'auditable_label' => $auditableLabel,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'http_method' => $request->method(),
                'request_path' => '/' . ltrim($request->path(), '/'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
    }
}
