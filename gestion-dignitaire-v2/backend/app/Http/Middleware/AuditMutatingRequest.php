<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuditMutatingRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        $requestId = (string) Str::uuid();
        $request->attributes->set('audit_request_id', $requestId);

        DB::beginTransaction();

        try {
            $response = $next($request);

            if ($response->getStatusCode() >= 400) {
                DB::rollBack();
                return $response;
            }

            if (!AuditLog::where('request_id', $requestId)->exists()) {
                $this->createFallbackLog($request, $response, $requestId);
            } else {
                AuditLog::where('request_id', $requestId)
                    ->whereNull('response_status')
                    ->update(['response_status' => $response->getStatusCode()]);
            }

            DB::commit();

            return $response;
        } catch (\Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function createFallbackLog(Request $request, Response $response, string $requestId): void
    {
        $causer = $request->user();
        $controller = class_basename((string) $request->route()?->getControllerClass());
        $type = str_replace('Controller', '', $controller) ?: 'HttpRequest';
        $routeParameters = array_values($request->route()?->parameters() ?? []);
        $numericParameters = array_values(array_filter($routeParameters, fn ($value) => is_numeric($value)));

        AuditLog::create([
            'request_id' => $requestId,
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer?->id,
            'causer_label' => $causer?->username
                ?? $causer?->nom_complet
                ?? $causer?->email
                ?? null,
            'action' => $this->action($request),
            'auditable_type' => $type,
            'auditable_id' => $numericParameters ? (int) end($numericParameters) : null,
            'auditable_label' => $request->route()?->getName(),
            'old_values' => null,
            'new_values' => ['journalisation' => 'automatique'],
            'http_method' => $request->method(),
            'request_path' => '/' . ltrim($request->path(), '/'),
            'response_status' => $response->getStatusCode(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    private function action(Request $request): string
    {
        $method = strtolower((string) $request->route()?->getActionMethod());

        return match ($method) {
            'store', 'register' => 'created',
            'destroy', 'deleteenfant' => 'deleted',
            'restaurer' => 'restored',
            'archiver' => 'archived',
            'valider', 'validerdocument', 'validerdiplome', 'validerexperience' => 'validated',
            'refuser', 'rejeterdocument', 'rejeterdiplome', 'rejeterexperience' => 'refused',
            'cloturer', 'terminerunion' => 'cloturee',
            'login' => 'connexion',
            'logout' => 'deconnexion',
            default => 'updated',
        };
    }
}
