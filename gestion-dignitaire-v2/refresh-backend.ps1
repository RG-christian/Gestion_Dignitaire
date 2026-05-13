Write-Host "🔄 Rafraîchissement du backend..." -ForegroundColor Green

Set-Location backend

Write-Host "🧹 Nettoyage des caches..." -ForegroundColor Yellow
php artisan cache:clear
php artisan config:clear
php artisan route:clear

Write-Host "⚡ Rechargement des configurations..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache

Write-Host "✅ Backend rafraîchi! Redémarrez le serveur avec: php artisan serve" -ForegroundColor Green
