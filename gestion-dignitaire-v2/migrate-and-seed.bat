@echo off
echo ========================================
echo Migration et Seeding de la base de donnees
echo ========================================
echo.

cd backend

echo [1/3] Suppression des anciennes tables et recreation...
php artisan migrate:fresh
echo.

echo [2/3] Execution du seeder avec les donnees initiales...
php artisan db:seed --class=InitialDataSeeder
echo.

echo [3/3] Nettoyage du cache...
php artisan config:clear
php artisan cache:clear
echo.

echo ========================================
echo Migration terminee avec succes!
echo ========================================
echo.
echo La base de donnees a ete recreee avec toutes les tables et donnees.
echo.
pause
