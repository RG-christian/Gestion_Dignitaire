<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DignitaireController;
use App\Http\Controllers\Api\NominationController;
use App\Http\Controllers\Api\DecorationController;
use App\Http\Controllers\Api\ReferentielController;

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

    // Expériences
    Route::post('/dignitaires/{id}/experiences', [DignitaireController::class, 'addExperience']);
    Route::put('/experiences/{id}', [DignitaireController::class, 'updateExperience']);
    Route::delete('/experiences/{id}', [DignitaireController::class, 'deleteExperience']);

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
});
