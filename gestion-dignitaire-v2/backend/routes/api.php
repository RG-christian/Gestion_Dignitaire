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
use App\Http\Controllers\Api\StructureController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

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

    // Dignitaires
    Route::apiResource('dignitaires', DignitaireController::class);
    Route::get('/dignitaires-stats', [DignitaireController::class, 'stats']);

    // Nominations
    Route::apiResource('nominations', NominationController::class);

    // Décorations
    Route::apiResource('decorations', DecorationController::class);
    Route::post('/dignitaires/{id}/decorations', [DecorationController::class, 'attachToDignitaire']);

    // Diplômes
    Route::post('/dignitaires/{id}/diplomes', [DignitaireController::class, 'addDiplome']);
    Route::put('/diplomes/{id}', [DignitaireController::class, 'updateDiplome']);
    Route::delete('/diplomes/{id}', [DignitaireController::class, 'deleteDiplome']);

    // Enfants
    Route::post('/dignitaires/{id}/enfants', [DignitaireController::class, 'addEnfant']);
    Route::put('/enfants/{id}', [DignitaireController::class, 'updateEnfant']);
    Route::delete('/enfants/{id}', [DignitaireController::class, 'deleteEnfant']);
    Route::apiResource('enfants', \App\Http\Controllers\Api\EnfantController::class);

    // Diplômes
    Route::apiResource('diplomes', \App\Http\Controllers\Api\DiplomeController::class);

    // Langues parlées
    Route::apiResource('langues-parlees', LangueParleeController::class);

    // Expériences
    Route::apiResource('experiences', ExperienceController::class);

    // Postes
    Route::apiResource('postes', PosteController::class);

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
    Route::apiResource('pays-crud', PaysController::class);
    Route::apiResource('regions-crud', RegionController::class);
    Route::apiResource('villes-crud', VilleController::class);

    // Structures
    Route::apiResource('structures', StructureController::class);

    // Administration des utilisateurs (Superadmin uniquement)
    Route::prefix('admin')->group(function () {
        Route::get('/users', [AdminController::class, 'index']);
        Route::post('/users', [AdminController::class, 'store']);
        Route::put('/users/{id}', [AdminController::class, 'update']);
        Route::delete('/users/{id}', [AdminController::class, 'destroy']);
        Route::get('/roles', [AdminController::class, 'roles']);
        Route::get('/fonctions', [AdminController::class, 'fonctions']);
        Route::get('/sousfonctions', [AdminController::class, 'sousfonctions']);
    });
});
