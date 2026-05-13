#!/bin/bash

echo "🚀 Optimisation de l'application..."

cd backend

echo "📦 Installation des dépendances..."
composer install --optimize-autoloader --no-dev

echo "🗄️  Exécution des migrations..."
php artisan migrate --force

echo "⚡ Optimisation de Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🧹 Nettoyage du cache..."
php artisan cache:clear
php artisan config:clear

echo "✅ Optimisation terminée!"
