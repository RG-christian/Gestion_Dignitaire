# Script d'installation complète - Gestion Dignitaire v2
# Exécuter avec : .\install-complete.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  INSTALLATION GESTION DIGNITAIRE V2   " -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$ErrorActionPreference = "Stop"

# Fonction pour afficher les étapes
function Write-Step {
    param($message)
    Write-Host "`n>>> $message" -ForegroundColor Yellow
}

# Fonction pour afficher le succès
function Write-Success {
    param($message)
    Write-Host "✓ $message" -ForegroundColor Green
}

# Fonction pour afficher les erreurs
function Write-Error-Custom {
    param($message)
    Write-Host "✗ $message" -ForegroundColor Red
}

try {
    # ÉTAPE 1 : Sauvegarder les fichiers générés
    Write-Step "Sauvegarde des fichiers générés..."
    
    if (Test-Path "backend-templates") {
        Remove-Item -Recurse -Force "backend-templates"
    }
    
    if (Test-Path "backend") {
        Rename-Item "backend" "backend-templates"
        Write-Success "Fichiers sauvegardés dans backend-templates/"
    }

    # ÉTAPE 2 : Installer Laravel
    Write-Step "Installation de Laravel 10 (cela peut prendre quelques minutes)..."
    
    composer create-project laravel/laravel backend "^10.0" --prefer-dist --no-interaction
    
    if ($LASTEXITCODE -ne 0) {
        throw "Erreur lors de l'installation de Laravel"
    }
    
    Write-Success "Laravel installé avec succès"

    # ÉTAPE 3 : Copier les fichiers générés
    Write-Step "Copie des fichiers générés..."
    
    # Copier les Models
    Copy-Item "backend-templates\app\Models\*" "backend\app\Models\" -Force
    Write-Success "Models copiés"
    
    # Créer le dossier Api et copier les Controllers
    New-Item -ItemType Directory -Force -Path "backend\app\Http\Controllers\Api" | Out-Null
    Copy-Item "backend-templates\app\Http\Controllers\Api\*" "backend\app\Http\Controllers\Api\" -Force
    Write-Success "Controllers copiés"
    
    # Copier les Migrations
    Copy-Item "backend-templates\database\migrations\*.php" "backend\database\migrations\" -Force
    Write-Success "Migrations copiées"
    
    # Copier les Routes
    Copy-Item "backend-templates\routes\api.php" "backend\routes\api.php" -Force
    Write-Success "Routes copiées"

    # ÉTAPE 4 : Configurer .env
    Write-Step "Configuration de l'environnement..."
    
    Copy-Item "backend\.env.example" "backend\.env" -Force
    
    # Générer la clé d'application
    Set-Location "backend"
    php artisan key:generate --no-interaction
    Set-Location ".."
    
    Write-Success "Fichier .env configuré"

    # ÉTAPE 5 : Installer Sanctum
    Write-Step "Installation de Laravel Sanctum..."
    
    Set-Location "backend"
    composer require laravel/sanctum --no-interaction
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --no-interaction
    Set-Location ".."
    
    Write-Success "Sanctum installé"

    # ÉTAPE 6 : Installer les dépendances Frontend
    Write-Step "Installation des dépendances Frontend (npm install)..."
    
    Set-Location "frontend"
    npm install
    
    if ($LASTEXITCODE -ne 0) {
        throw "Erreur lors de l'installation des dépendances npm"
    }
    
    # Copier .env
    Copy-Item ".env.example" ".env" -Force
    
    Set-Location ".."
    
    Write-Success "Dépendances Frontend installées"

    # ÉTAPE 7 : Afficher les instructions finales
    Write-Host "`n========================================" -ForegroundColor Cyan
    Write-Host "  INSTALLATION TERMINÉE AVEC SUCCÈS !  " -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "PROCHAINES ÉTAPES :" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "1. Configurer la base de données :" -ForegroundColor White
    Write-Host "   - Ouvrir backend\.env" -ForegroundColor Gray
    Write-Host "   - Modifier DB_DATABASE, DB_USERNAME, DB_PASSWORD" -ForegroundColor Gray
    Write-Host ""
    Write-Host "2. Créer la base de données :" -ForegroundColor White
    Write-Host "   mysql -u root -p" -ForegroundColor Gray
    Write-Host "   CREATE DATABASE gestion_dignitaire_v2;" -ForegroundColor Gray
    Write-Host "   EXIT;" -ForegroundColor Gray
    Write-Host ""
    Write-Host "3. Exécuter les migrations :" -ForegroundColor White
    Write-Host "   cd backend" -ForegroundColor Gray
    Write-Host "   php artisan migrate" -ForegroundColor Gray
    Write-Host ""
    Write-Host "4. Créer un utilisateur admin :" -ForegroundColor White
    Write-Host "   php artisan tinker" -ForegroundColor Gray
    Write-Host "   (Voir LISEZ-MOI-DABORD.md pour les commandes)" -ForegroundColor Gray
    Write-Host ""
    Write-Host "5. Démarrer les serveurs :" -ForegroundColor White
    Write-Host "   Terminal 1: cd backend && php artisan serve" -ForegroundColor Gray
    Write-Host "   Terminal 2: cd frontend && npm run dev" -ForegroundColor Gray
    Write-Host ""
    Write-Host "6. Ouvrir l'application :" -ForegroundColor White
    Write-Host "   http://localhost:3000" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Pour plus de détails, consultez LISEZ-MOI-DABORD.md" -ForegroundColor Cyan
    Write-Host ""

} catch {
    Write-Host "`n========================================" -ForegroundColor Red
    Write-Host "  ERREUR LORS DE L'INSTALLATION" -ForegroundColor Red
    Write-Host "========================================" -ForegroundColor Red
    Write-Host ""
    Write-Host "Erreur: $_" -ForegroundColor Red
    Write-Host ""
    Write-Host "Consultez INSTALLATION_MANUELLE.md pour une installation pas à pas" -ForegroundColor Yellow
    exit 1
}
