<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DignitaireController;
use App\Http\Controllers\Api\NominationController;
use App\Http\Controllers\Api\DecorationController;
use App\Http\Controllers\Api\ReferentielController;
use App\Http\Controllers\Api\PosteController;
use App\Http\Controllers\Api\LangueParleeController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\PaysController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\VilleController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\StructureController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\CandidatAuthController;
use App\Http\Controllers\Api\CandidatController;
use App\Http\Controllers\Api\CandidatDocumentController;
use App\Http\Controllers\Api\CandidatDiplomeController;
use App\Http\Controllers\Api\CandidatLangueController;
use App\Http\Controllers\Api\CandidatExperienceController;
use App\Http\Controllers\Api\ConjointController;
use App\Http\Controllers\Api\DignitaireDocumentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Routes publiques - Authentification Admin
Route::post('/login', [AuthController::class, 'login']);

// Routes publiques - Candidats (inscription et connexion)
Route::prefix('candidats')->group(function () {
    Route::post('/register', [CandidatAuthController::class, 'register']);
    Route::post('/login', [CandidatAuthController::class, 'login']);
});

// Routes publiques - Référentiels & statistiques pour la page d'accueil / inscription
Route::get('/public/stats', [DashboardController::class, 'publicStats']);
Route::get('/public/pays', [ReferentielController::class, 'pays']);
Route::get('/public/villes', [ReferentielController::class, 'villes']);

// Route publique pour servir les photos de profil
Route::get('/uploads/photos/{filename}', function($filename) {
    $path = public_path('uploads/photos/' . $filename);
    if (!file_exists($path)) {
        abort(404, 'Photo not found');
    }
    return response()->file($path);
});

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Profil utilisateur
    Route::get('/profile/test', function() {
        return response()->json(['message' => 'Profile routes working!']);
    });
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // Route de test pour vérifier les fonctions
    Route::get('/test-fonctions', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'user_id' => $user->id,
            'fonctions_count' => $user->fonctions()->count(),
            'sousfonctions_count' => $user->sousfonctions()->count(),
            'fonctions' => $user->fonctions,
            'sousfonctions' => $user->sousfonctions,
        ]);
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']); // Endpoint optimisé
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData']);

    // Dignitaires
    Route::middleware('permission:Dignitaire')->group(function () {
        Route::apiResource('dignitaires', DignitaireController::class);
        Route::get('/dignitaires-stats', [DignitaireController::class, 'stats']);
        Route::get('/dignitaires-export', [DignitaireController::class, 'export']);
        Route::get('/dignitaires/{id}/export-pdf', [DignitaireController::class, 'exportFichePdf']);
    });

    // Nominations
    Route::middleware('permission:Nomination')->group(function () {
        Route::apiResource('nominations', NominationController::class);
        Route::post('/nominations/{id}/cloturer', [NominationController::class, 'cloturer']);
        Route::get('/nominations-export', [NominationController::class, 'export']);
    });

    // Décorations
    Route::middleware('permission:Décoration')->group(function () {
        Route::apiResource('decorations', DecorationController::class);
        Route::post('/dignitaires/{id}/decorations', [DecorationController::class, 'attachToDignitaire']);
        Route::get('/decorations-export', [DecorationController::class, 'export']);
    });

    // Diplômes
    Route::middleware('permission:Diplôme')->group(function () {
        Route::post('/dignitaires/{id}/diplomes', [DignitaireController::class, 'addDiplome']);
        Route::put('/diplomes/{id}', [DignitaireController::class, 'updateDiplome']);
        Route::delete('/diplomes/{id}', [DignitaireController::class, 'deleteDiplome']);
        Route::apiResource('diplomes', \App\Http\Controllers\Api\DiplomeController::class);
        Route::get('/diplomes-export', [\App\Http\Controllers\Api\DiplomeController::class, 'export']);
    });

    // Enfants
    Route::middleware('permission:Enfant')->group(function () {
        Route::post('/dignitaires/{id}/enfants', [DignitaireController::class, 'addEnfant']);
        Route::put('/enfants/{id}', [DignitaireController::class, 'updateEnfant']);
        Route::delete('/enfants/{id}', [DignitaireController::class, 'deleteEnfant']);
        Route::apiResource('enfants', \App\Http\Controllers\Api\EnfantController::class);
    });

    // Langues parlées
    Route::middleware('permission:Langues')->group(function () {
        Route::apiResource('langues-parlees', LangueParleeController::class);
    });

    // Expériences
    Route::middleware('permission:Expérience')->group(function () {
        Route::apiResource('experiences', ExperienceController::class);
    });

    // Postes
    Route::middleware('permission:Poste')->group(function () {
        Route::apiResource('postes', PosteController::class);
        Route::post('/postes/{id}/cloturer', [PosteController::class, 'cloturer']);
        Route::get('/postes-export', [PosteController::class, 'export']);
    });

    // Entités (CRUD complet) — pas de sous-fonction dédiée à ce jour, laissé ouvert à tout utilisateur authentifié
    Route::apiResource('entites', \App\Http\Controllers\Api\EntiteController::class);
    Route::get('/entites-export', [\App\Http\Controllers\Api\EntiteController::class, 'export']);

    // Référentiels (lecture seule)
    Route::get('/pays', [ReferentielController::class, 'pays']);
    Route::get('/regions', [ReferentielController::class, 'regions']);
    Route::get('/villes', [ReferentielController::class, 'villes']);
    Route::get('/entites', [ReferentielController::class, 'entites']);
    Route::get('/langues', [ReferentielController::class, 'langues']);
    Route::get('/domaines', [ReferentielController::class, 'domaines']);
    Route::get('/structures', [ReferentielController::class, 'structures']);
    Route::get('/etablissements', [ReferentielController::class, 'etablissements']);

    // Gestion Pays, Régions, Villes (CRUD complet)
    Route::middleware('permission:Pays')->group(function () {
        Route::apiResource('pays-crud', PaysController::class);
    });
    Route::middleware('permission:Région')->group(function () {
        Route::apiResource('regions-crud', RegionController::class);
    });
    Route::middleware('permission:Ville')->group(function () {
        Route::apiResource('villes-crud', VilleController::class);
    });

    // Structures
    Route::middleware('permission:Structure')->group(function () {
        Route::apiResource('structures', StructureController::class);
        Route::get('/structures-export', [StructureController::class, 'export']);
    });

    // Candidats - Routes protégées pour candidats connectés
    Route::prefix('candidats')->group(function () {
        Route::post('/logout', [CandidatAuthController::class, 'logout']);
        Route::get('/me', [CandidatAuthController::class, 'me']);
        Route::put('/me', [CandidatAuthController::class, 'updateProfile']);

        // Documents du candidat
        Route::get('/me/documents', [CandidatDocumentController::class, 'index']);
        Route::post('/me/documents', [CandidatDocumentController::class, 'store']);
        Route::delete('/me/documents/{id}', [CandidatDocumentController::class, 'destroy']);

        // Diplômes du candidat
        Route::get('/me/diplomes', [CandidatDiplomeController::class, 'index']);
        Route::post('/me/diplomes', [CandidatDiplomeController::class, 'store']);
        Route::put('/me/diplomes/{id}', [CandidatDiplomeController::class, 'update']);
        Route::delete('/me/diplomes/{id}', [CandidatDiplomeController::class, 'destroy']);

        // Langues du candidat
        Route::get('/me/langues', [CandidatLangueController::class, 'index']);
        Route::post('/me/langues', [CandidatLangueController::class, 'store']);
        Route::delete('/me/langues/{id}', [CandidatLangueController::class, 'destroy']);

        // Expériences professionnelles du candidat
        Route::get('/me/experiences', [CandidatExperienceController::class, 'index']);
        Route::post('/me/experiences', [CandidatExperienceController::class, 'store']);
        Route::put('/me/experiences/{id}', [CandidatExperienceController::class, 'update']);
        Route::delete('/me/experiences/{id}', [CandidatExperienceController::class, 'destroy']);
    });

    // Conjoints
    Route::middleware('permission:Dignitaire')->group(function () {
        Route::prefix('dignitaires/{dignitaireId}')->group(function () {
            Route::get('/conjoints', [ConjointController::class, 'index']);
            Route::post('/conjoints', [ConjointController::class, 'store']);
        });
        Route::prefix('conjoints')->group(function () {
            Route::get('/{id}', [ConjointController::class, 'show']);
            Route::put('/{id}', [ConjointController::class, 'update']);
            Route::delete('/{id}', [ConjointController::class, 'destroy']);
            Route::post('/{id}/terminer-union', [ConjointController::class, 'terminerUnion']);
        });
    });

    // Documents dignitaires (BLOC 6 - Gestion Documentaire)
    Route::middleware('permission:Dignitaire')->group(function () {
        Route::prefix('dignitaires/{dignitaireId}')->group(function () {
            Route::get('/documents', [DignitaireDocumentController::class, 'index']);
            Route::post('/documents', [DignitaireDocumentController::class, 'store']);
        });
        Route::prefix('dignitaire-documents')->group(function () {
            Route::get('/{id}/download', [DignitaireDocumentController::class, 'download']);
            Route::delete('/{id}', [DignitaireDocumentController::class, 'destroy']);
        });
    });

    // Administration des utilisateurs (Super Administrateur uniquement)
    Route::prefix('admin')->group(function () {
        Route::get('/audit-logs', [AuditLogController::class, 'index']);

        // Rapports périodiques archivés
        Route::get('/rapports', [\App\Http\Controllers\Api\RapportController::class, 'index']);
        Route::get('/rapports/{id}/download', [\App\Http\Controllers\Api\RapportController::class, 'download']);

        Route::middleware('super-admin')->group(function () {
            Route::get('/users', [AdminController::class, 'index']);
            Route::get('/users/{id}', [AdminController::class, 'show']);
            Route::post('/users', [AdminController::class, 'store']);
            Route::put('/users/{id}', [AdminController::class, 'update']);
            Route::delete('/users/{id}', [AdminController::class, 'destroy']);
            Route::get('/roles', [AdminController::class, 'roles']);
            Route::get('/fonctions', [AdminController::class, 'fonctions']);
            Route::get('/sousfonctions', [AdminController::class, 'sousfonctions']);
        });

        // Gestion des candidatures (Admin uniquement)
        Route::get('/candidats', [CandidatController::class, 'index']);
        Route::get('/candidats/stats', [CandidatController::class, 'stats']);
        Route::get('/candidats/{id}', [CandidatController::class, 'show']);
        Route::post('/candidats/{id}/valider', [CandidatController::class, 'valider']);
        Route::post('/candidats/{id}/refuser', [CandidatController::class, 'refuser']);
        Route::delete('/candidats/{id}', [CandidatController::class, 'destroy']);
        Route::get('/candidats/{candidatId}/documents/{documentId}/download', [CandidatDocumentController::class, 'download']);
    });
});
