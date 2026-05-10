# Script d'installation automatique pour Windows
# Gestion Dignitaires v2

Write-Host "========================================" -ForegroundColor Green
Write-Host "  Installation Gestion Dignitaires v2  " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Vérifier si on est dans le bon dossier
if (-not (Test-Path ".\backend") -or -not (Test-Path ".\frontend")) {
    Write-Host "ERREUR: Vous devez executer ce script depuis le dossier gestion-dignitaire-v2" -ForegroundColor Red
    exit 1
}

# Demander les informations de base de données
Write-Host "Configuration de la base de donnees" -ForegroundColor Yellow
$dbName = Read-Host "Nom de la base de donnees [gestion_dignitaire_v2]"
if ([string]::IsNullOrWhiteSpace($dbName)) { $dbName = "gestion_dignitaire_v2" }

$dbUser = Read-Host "Utilisateur MySQL [root]"
if ([string]::IsNullOrWhiteSpace($dbUser)) { $dbUser = "root" }

$dbPass = Read-Host "Mot de passe MySQL [root]" -AsSecureString
$dbPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($dbPass))
if ([string]::IsNullOrWhiteSpace($dbPassPlain)) { $dbPassPlain = "root" }

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  ETAPE 1: Installation Backend Laravel" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green

# Sauvegarder l'ancien backend
if (Test-Path ".\backend\app") {
    Write-Host "Sauvegarde de l'ancien backend..." -ForegroundColor Yellow
    if (Test-Path ".\backend-old") {
        Remove-Item -Recurse -Force ".\backend-old"
    }
    Rename-Item ".\backend" "backend-old"
}

# Créer nouveau Laravel
Write-Host "Creation du projet Laravel..." -ForegroundColor Yellow
composer create-project laravel/laravel backend "^10.0" --prefer-dist

if (-not $?) {
    Write-Host "ERREUR: Echec de l'installation de Laravel" -ForegroundColor Red
    exit 1
}

# Copier les fichiers générés
if (Test-Path ".\backend-old") {
    Write-Host "Copie des fichiers generes..." -ForegroundColor Yellow
    
    # Models
    if (Test-Path ".\backend-old\app\Models") {
        Copy-Item ".\backend-old\app\Models\*" ".\backend\app\Models\" -Recurse -Force
    }
    
    # Controllers
    if (Test-Path ".\backend-old\app\Http\Controllers\Api") {
        New-Item -ItemType Directory -Force -Path ".\backend\app\Http\Controllers\Api" | Out-Null
        Copy-Item ".\backend-old\app\Http\Controllers\Api\*" ".\backend\app\Http\Controllers\Api\" -Recurse -Force
    }
    
    # Migrations
    if (Test-Path ".\backend-old\database\migrations") {
        Copy-Item ".\backend-old\database\migrations\*.php" ".\backend\database\migrations\" -Force
    }
    
    # Routes
    if (Test-Path ".\backend-old\routes\api.php") {
        Copy-Item ".\backend-old\routes\api.php" ".\backend\routes\api.php" -Force
    }
}

# Configuration
Write-Host "Configuration de Laravel..." -ForegroundColor Yellow
Set-Location backend

# Copier .env
Copy-Item ".env.example" ".env" -Force

# Générer la clé
php artisan key:generate

# Mettre à jour .env
$envContent = Get-Content ".env" -Raw
$envContent = $envContent -replace "DB_DATABASE=.*", "DB_DATABASE=$dbName"
$envContent = $envContent -replace "DB_USERNAME=.*", "DB_USERNAME=$dbUser"
$envContent = $envContent -replace "DB_PASSWORD=.*", "DB_PASSWORD=$dbPassPlain"
$envContent += "`nSANCTUM_STATEFUL_DOMAINS=localhost:3000`nSESSION_DOMAIN=localhost"
Set-Content ".env" $envContent

Write-Host "Configuration terminee!" -ForegroundColor Green

# Créer la base de données
Write-Host "Creation de la base de donnees..." -ForegroundColor Yellow
$createDbQuery = "CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u $dbUser -p$dbPassPlain -e $createDbQuery 2>$null

if ($?) {
    Write-Host "Base de donnees creee!" -ForegroundColor Green
} else {
    Write-Host "ATTENTION: Impossible de creer la base de donnees automatiquement" -ForegroundColor Yellow
    Write-Host "Creez-la manuellement avec phpMyAdmin" -ForegroundColor Yellow
}

# Installer Sanctum
Write-Host "Installation de Laravel Sanctum..." -ForegroundColor Yellow
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Migrations
Write-Host "Execution des migrations..." -ForegroundColor Yellow
php artisan migrate --force

if (-not $?) {
    Write-Host "ERREUR: Echec des migrations" -ForegroundColor Red
    Write-Host "Verifiez votre configuration de base de donnees" -ForegroundColor Yellow
}

# Créer utilisateur admin
Write-Host "Creation de l'utilisateur admin..." -ForegroundColor Yellow
$createUserScript = @"
`$user = new App\Models\User();
`$user->username = 'admin';
`$user->nom_complet = 'Administrateur';
`$user->email = 'admin@example.com';
`$user->password = bcrypt('password');
`$user->role_id = 1;
`$user->save();
echo 'Utilisateur admin cree!';
"@

$createUserScript | php artisan tinker

Set-Location ..

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  ETAPE 2: Installation Frontend Nuxt  " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green

Set-Location frontend

# Copier .env
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env" -Force
}

# Installer dépendances
Write-Host "Installation des dependances npm..." -ForegroundColor Yellow
npm install

if (-not $?) {
    Write-Host "ERREUR: Echec de l'installation npm" -ForegroundColor Red
    exit 1
}

Set-Location ..

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  INSTALLATION TERMINEE!               " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Pour demarrer l'application:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Backend (dans un terminal):" -ForegroundColor Cyan
Write-Host "   cd backend" -ForegroundColor White
Write-Host "   php artisan serve" -ForegroundColor White
Write-Host ""
Write-Host "2. Frontend (dans un autre terminal):" -ForegroundColor Cyan
Write-Host "   cd frontend" -ForegroundColor White
Write-Host "   npm run dev" -ForegroundColor White
Write-Host ""
Write-Host "3. Ouvrir http://localhost:3000" -ForegroundColor Cyan
Write-Host "   Login: admin" -ForegroundColor White
Write-Host "   Password: password" -ForegroundColor White
Write-Host ""
Write-Host "Bon developpement! " -ForegroundColor Green
