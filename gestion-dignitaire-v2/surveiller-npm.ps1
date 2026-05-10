# Script pour surveiller la progression de npm install
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  SURVEILLANCE NPM INSTALL" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$startTime = Get-Date

while ($true) {
    Clear-Host
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host "  SURVEILLANCE NPM INSTALL" -ForegroundColor Cyan
    Write-Host "========================================" -ForegroundColor Cyan
    Write-Host ""
    
    $elapsed = (Get-Date) - $startTime
    Write-Host "Temps écoulé: $($elapsed.Minutes)m $($elapsed.Seconds)s" -ForegroundColor Yellow
    Write-Host ""
    
    if (Test-Path "frontend/node_modules") {
        $count = (Get-ChildItem "frontend/node_modules" -Directory -ErrorAction SilentlyContinue).Count
        $size = (Get-ChildItem "frontend/node_modules" -Recurse -File -ErrorAction SilentlyContinue | Measure-Object -Property Length -Sum).Sum / 1MB
        
        Write-Host "✓ Installation en cours..." -ForegroundColor Green
        Write-Host ""
        Write-Host "Packages installés: $count / ~1500" -ForegroundColor Cyan
        Write-Host "Taille actuelle: $([math]::Round($size, 2)) MB / ~400 MB" -ForegroundColor Cyan
        
        $progress = [math]::Min(100, ($count / 1500) * 100)
        Write-Host ""
        Write-Host "Progression: $([math]::Round($progress, 1))%" -ForegroundColor Yellow
        
        # Barre de progression
        $barLength = 40
        $filled = [math]::Floor($barLength * $progress / 100)
        $empty = $barLength - $filled
        $bar = "[" + ("█" * $filled) + ("░" * $empty) + "]"
        Write-Host $bar -ForegroundColor Green
        
        if ($count -ge 1400) {
            Write-Host ""
            Write-Host "✓ Presque terminé ! Finalisation en cours..." -ForegroundColor Green
        }
    } else {
        Write-Host "⏳ Initialisation..." -ForegroundColor Yellow
        Write-Host "   npm télécharge les informations des packages..." -ForegroundColor Gray
    }
    
    Write-Host ""
    Write-Host "Appuyez sur Ctrl+C pour arrêter la surveillance" -ForegroundColor Gray
    Write-Host "(npm install continue en arrière-plan)" -ForegroundColor Gray
    
    Start-Sleep -Seconds 5
}
