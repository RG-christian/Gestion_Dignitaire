# Installation Frontend uniquement
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  INSTALLATION FRONTEND" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Set-Location "frontend"

Write-Host "Installation des dépendances npm..." -ForegroundColor Yellow
Write-Host "(Cela peut prendre 5-10 minutes)" -ForegroundColor Gray
Write-Host ""

npm install

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n✓ Dépendances npm installées avec succès" -ForegroundColor Green
    
    # Créer .env
    if (-not (Test-Path ".env")) {
        Copy-Item ".env.example" ".env" -Force
        Write-Host "✓ Fichier .env créé" -ForegroundColor Green
    }
    
    Write-Host "`n========================================" -ForegroundColor Cyan
    Write-Host "  INSTALLATION TERMINÉE !" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Pour démarrer le frontend :" -ForegroundColor Yellow
    Write-Host "  npm run dev" -ForegroundColor White
    Write-Host ""
} else {
    Write-Host "`n✗ Erreur lors de l'installation" -ForegroundColor Red
}

Set-Location ".."
