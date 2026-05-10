# Installation npm simple avec progression
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  INSTALLATION NPM FRONTEND" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Cela va installer environ 1500-2000 packages (~400 MB)" -ForegroundColor Yellow
Write-Host "Temps estimé: 10-20 minutes selon votre connexion" -ForegroundColor Yellow
Write-Host ""
Write-Host "Démarrage de l'installation..." -ForegroundColor Green
Write-Host ""

Set-Location frontend

# Lancer npm install
npm install

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "  ✓ INSTALLATION TERMINÉE !" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    
    # Créer .env si nécessaire
    if (-not (Test-Path ".env")) {
        Copy-Item ".env.example" ".env" -Force
        Write-Host "✓ Fichier .env créé" -ForegroundColor Green
    }
    
    # Afficher les stats
    $count = (Get-ChildItem node_modules -Directory -ErrorAction SilentlyContinue).Count
    Write-Host "Packages installés: $count" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Pour démarrer le frontend:" -ForegroundColor Yellow
    Write-Host "  npm run dev" -ForegroundColor White
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "✗ Erreur lors de l'installation" -ForegroundColor Red
    Write-Host ""
    Write-Host "Essayez:" -ForegroundColor Yellow
    Write-Host "  npm cache clean --force" -ForegroundColor White
    Write-Host "  npm install" -ForegroundColor White
}

Set-Location ..
