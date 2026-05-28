# Script pour redémarrer le frontend Nuxt
Write-Host "🔄 Redémarrage du frontend Nuxt..." -ForegroundColor Cyan

# Arrêter les processus Node existants sur le port 3000
Write-Host "🛑 Arrêt des processus existants..." -ForegroundColor Yellow
$processes = Get-NetTCPConnection -LocalPort 3000 -ErrorAction SilentlyContinue | Select-Object -ExpandProperty OwningProcess -Unique
if ($processes) {
    foreach ($proc in $processes) {
        Stop-Process -Id $proc -Force -ErrorAction SilentlyContinue
        Write-Host "   ✓ Processus $proc arrêté" -ForegroundColor Green
    }
} else {
    Write-Host "   ℹ Aucun processus sur le port 3000" -ForegroundColor Gray
}

# Attendre un peu
Start-Sleep -Seconds 2

# Démarrer le serveur de développement
Write-Host ""
Write-Host "🚀 Démarrage du serveur de développement..." -ForegroundColor Cyan
Write-Host "   📍 URL: http://localhost:3000" -ForegroundColor White
Write-Host "   ⏹️  Pour arrêter: Ctrl+C" -ForegroundColor Gray
Write-Host ""

Set-Location frontend
npm run dev
