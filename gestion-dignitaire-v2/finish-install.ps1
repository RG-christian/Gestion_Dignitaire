# Script pour terminer l'installation
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  FINALISATION DE L'INSTALLATION" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

$ErrorActionPreference = "Continue"

# ÉTAPE 1 : Copier les fichiers générés
Write-Host "`n>>> Copie des fichiers générés..." -ForegroundColor Yellow

# Copier les Models
Write-Host "Copie des Models..." -ForegroundColor Gray
Copy-Item "backend-templates\app\Models\*" "backend\app\Models\" -Force
Write-Host "✓ Models copiés" -ForegroundColor Green

# Créer le dossier Api et copier les Controllers
Write-Host "Copie des Controllers..." -ForegroundColor Gray
New-Item -ItemType Directory -Force -Path "backend\app\Http\Controllers\Api" | Out-Null
Copy-Item "backend-templates\app\Http\Controllers\Api\*" "backend\app\Http\Controllers\Api\" -Force
Write-Host "✓ Controllers copiés" -ForegroundColor Green

# Copier les Migrations
Write-Host "Copie des Migrations..." -ForegroundColor Gray
Copy-Item "backend-templates\database\migrations\*.php" "backend\database\migrations\" -Force
Write-Host "✓ Migrations copiées" -ForegroundColor Green

# Copier les Routes
Write-Host "Copie des Routes..." -ForegroundColor Gray
Copy-Item "backend-templates\routes\api.php" "backend\routes\api.php" -Force
Write-Host "✓ Routes copiées" -ForegroundColor Green

# ÉTAPE 2 : Générer la clé Laravel
Write-Host "`n>>> Configuration de Laravel..." -ForegroundColor Yellow
Set-Location "backend"
php artisan key:generate --no-interaction
Write-Host "✓ Clé Laravel générée" -ForegroundColor Green
Set-Location ".."

# ÉTAPE 3 : Installer Sanctum
Write-Host "`n>>> Installation de Laravel Sanctum..." -ForegroundColor Yellow
Set-Location "backend"
composer require laravel/sanctum --no-interaction
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --no-interaction
Write-Host "✓ Sanctum installé" -ForegroundColor Green
Set-Location ".."

# ÉTAPE 4 : Installer les dépendances Frontend
Write-Host "`n>>> Installation des dépendances Frontend..." -ForegroundColor Yellow
Set-Location "frontend"
npm install
Copy-Item ".env.example" ".env" -Force
Write-Host "✓ Frontend configuré" -ForegroundColor Green
Set-Location ".."

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  INSTALLATION TERMINÉE !" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "PROCHAINES ÉTAPES :" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Configurer la base de données dans backend\.env" -ForegroundColor White
Write-Host "   DB_DATABASE=gestion_dignitaire_v2" -ForegroundColor Gray
Write-Host "   DB_USERNAME=root" -ForegroundColor Gray
Write-Host "   DB_PASSWORD=root" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Créer la base de données :" -ForegroundColor White
Write-Host "   mysql -u root -p" -ForegroundColor Gray
Write-Host "   CREATE DATABASE gestion_dignitaire_v2;" -ForegroundColor Gray
Write-Host ""
Write-Host "3. Exécuter les migrations :" -ForegroundColor White
Write-Host "   cd backend" -ForegroundColor Gray
Write-Host "   php artisan migrate" -ForegroundColor Gray
Write-Host ""
Write-Host "4. Démarrer les serveurs :" -ForegroundColor White
Write-Host "   Terminal 1: cd backend && php artisan serve" -ForegroundColor Gray
Write-Host "   Terminal 2: cd frontend && npm run dev" -ForegroundColor Gray
Write-Host ""
