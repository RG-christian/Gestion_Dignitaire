Write-Host "🚀 Optimisation de l'application..." -ForegroundColor Green

Set-Location backend

Write-Host "📦 Installation des dépendances..." -ForegroundColor Yellow
composer install --optimize-autoloader

Write-Host "🗄️  Exécution des migrations..." -ForegroundColor Yellow
php artisan migrate --force

Write-Host "⚡ Optimisation de Laravel..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache

Write-Host "🧹 Nettoyage du cache..." -ForegroundColor Yellow
php artisan cache:clear

Write-Host "✅ Optimisation terminée!" -ForegroundColor Green
