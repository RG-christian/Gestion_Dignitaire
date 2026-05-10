# Script de vérification de l'installation
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  VÉRIFICATION DE L'INSTALLATION" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$allGood = $true

# Vérifier Laravel
Write-Host "1. Laravel Backend..." -ForegroundColor Yellow
if (Test-Path "backend/artisan") {
    Write-Host "   ✓ Laravel installé" -ForegroundColor Green
    if (Test-Path "backend/vendor") {
        Write-Host "   ✓ Dépendances Composer installées" -ForegroundColor Green
    } else {
        Write-Host "   ✗ Dépendances Composer manquantes" -ForegroundColor Red
        $allGood = $false
    }
} else {
    Write-Host "   ✗ Laravel non installé" -ForegroundColor Red
    $allGood = $false
}

# Vérifier les fichiers copiés
Write-Host "`n2. Fichiers générés..." -ForegroundColor Yellow
$modelsCount = (Get-ChildItem "backend/app/Models/*.php" -ErrorAction SilentlyContinue).Count
$controllersCount = (Get-ChildItem "backend/app/Http/Controllers/Api/*.php" -ErrorAction SilentlyContinue).Count
$migrationsCount = (Get-ChildItem "backend/database/migrations/2024_*.php" -ErrorAction SilentlyContinue).Count

Write-Host "   ✓ $modelsCount Models copiés" -ForegroundColor Green
Write-Host "   ✓ $controllersCount Controllers copiés" -ForegroundColor Green
Write-Host "   ✓ $migrationsCount Migrations copiées" -ForegroundColor Green

# Vérifier Sanctum
Write-Host "`n3. Laravel Sanctum..." -ForegroundColor Yellow
if (Test-Path "backend/config/sanctum.php") {
    Write-Host "   ✓ Sanctum installé" -ForegroundColor Green
} else {
    Write-Host "   ✗ Sanctum non installé" -ForegroundColor Red
    $allGood = $false
}

# Vérifier Frontend
Write-Host "`n4. Frontend Nuxt..." -ForegroundColor Yellow
if (Test-Path "frontend/node_modules") {
    Write-Host "   ✓ Dépendances npm installées" -ForegroundColor Green
} else {
    Write-Host "   ⏳ Installation npm en cours..." -ForegroundColor Yellow
    Write-Host "   Attendez que 'npm install' se termine" -ForegroundColor Gray
    $allGood = $false
}

if (Test-Path "frontend/.env") {
    Write-Host "   ✓ Fichier .env créé" -ForegroundColor Green
} else {
    Write-Host "   ✗ Fichier .env manquant" -ForegroundColor Red
}

# Vérifier .env backend
Write-Host "`n5. Configuration..." -ForegroundColor Yellow
if (Test-Path "backend/.env") {
    Write-Host "   ✓ Fichier backend/.env existe" -ForegroundColor Green
    Write-Host "   ⚠ Vérifiez la configuration DB dans backend/.env" -ForegroundColor Yellow
} else {
    Write-Host "   ✗ Fichier backend/.env manquant" -ForegroundColor Red
    $allGood = $false
}

# Résumé
Write-Host "`n========================================" -ForegroundColor Cyan
if ($allGood) {
    Write-Host "  ✅ INSTALLATION COMPLÈTE !" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "PROCHAINES ÉTAPES :" -ForegroundColor Yellow
    Write-Host "1. Consultez CONFIGURATION_FINALE.md" -ForegroundColor White
    Write-Host "2. Configurez la base de données" -ForegroundColor White
    Write-Host "3. Exécutez les migrations" -ForegroundColor White
    Write-Host "4. Démarrez les serveurs" -ForegroundColor White
} else {
    Write-Host "  ⚠ INSTALLATION INCOMPLÈTE" -ForegroundColor Yellow
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Si 'npm install' est en cours, attendez qu'il se termine." -ForegroundColor Yellow
    Write-Host "Sinon, exécutez : .\finish-install.ps1" -ForegroundColor White
}
Write-Host ""
