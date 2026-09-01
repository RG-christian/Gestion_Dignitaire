# Guide d'Installation - Système de Gestion des Dignitaires

Ce guide vous permettra d'installer et de démarrer le projet sur un nouvel ordinateur.

---

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les éléments suivants :

### 1. Serveur Web (MAMP)
- **Windows** : Téléchargez MAMP depuis [https://www.mamp.info/](https://www.mamp.info/)
- **Configuration recommandée** :
  - PHP 8.1 ou supérieur
  - MySQL 5.7 ou supérieur
  - Apache

### 2. Composer (Gestionnaire de dépendances PHP)
- Téléchargez depuis [https://getcomposer.org/download/](https://getcomposer.org/download/)
- Vérifiez l'installation : `composer --version`

### 3. Node.js et NPM
- Téléchargez Node.js LTS depuis [https://nodejs.org/](https://nodejs.org/)
- NPM sera installé automatiquement avec Node.js
- Vérifiez l'installation :
  ```bash
  node --version
  npm --version
  ```

### 4. Git (optionnel)
- Téléchargez depuis [https://git-scm.com/](https://git-scm.com/)

---

## Étape 1 : Récupérer le Projet

### Option A : Avec Git
```bash
cd c:\MAMP\htdocs
git clone <url-du-repo> Gestion_Dignitaire
cd Gestion_Dignitaire\gestion-dignitaire-v2
```

### Option B : Sans Git
1. Copiez le dossier du projet dans `c:\MAMP\htdocs\`
2. Renommez-le en `Gestion_Dignitaire`

---

## Étape 2 : Configuration de MAMP

### 1. Démarrer MAMP
- Lancez l'application MAMP
- Cliquez sur "Start Servers"
- Vérifiez que Apache et MySQL sont actifs (indicateurs verts)

### 2. Configurer les ports (si nécessaire)
- Allez dans `Preferences > Ports`
- Apache Port : `80` ou `8888`
- MySQL Port : `3306` ou `8889`

### 3. Configurer PHP
- Allez dans `Preferences > PHP`
- Sélectionnez **PHP 8.1** ou supérieur

---

## Étape 3 : Configuration de la Base de Données

### 1. Créer la base de données
- Ouvrez phpMyAdmin : [http://localhost/phpMyAdmin](http://localhost/phpMyAdmin)
- **Identifiants par défaut** :
  - Utilisateur : `root`
  - Mot de passe : `root`
- Créez une nouvelle base de données nommée `gestion_dignitaire_v2`
- Collation : `utf8mb4_unicode_ci`

### 2. Importer les données (si vous avez un dump SQL)
```bash
# Depuis le terminal dans le dossier backend
mysql -u root -proot gestion_dignitaire_v2 < database/dump.sql
```

---

## Étape 4 : Installation du Backend (Laravel)

### 1. Naviguer vers le dossier backend
```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend
```

### 2. Installer les dépendances PHP
```bash
composer install
```

**Note** : Si vous rencontrez des erreurs, essayez :
```bash
composer install --ignore-platform-reqs
```

### 3. Configurer le fichier .env
```bash
# Copier le fichier d'exemple
copy .env.example .env
```

Ouvrez le fichier `.env` et modifiez les lignes suivantes :
```env
APP_NAME="Gestion Dignitaires"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_dignitaire_v2
DB_USERNAME=root
DB_PASSWORD=root

FRONTEND_URL=http://localhost:3000
```

### 4. Générer la clé d'application
```bash
php artisan key:generate
```

### 5. Créer le lien symbolique pour le storage
```bash
php artisan storage:link
```

### 6. Exécuter les migrations
```bash
php artisan migrate
```

**Si vous avez des seeders** (données de test) :
```bash
php artisan db:seed
```

**Ou tout en une seule commande** :
```bash
php artisan migrate:fresh --seed
```

### 7. Nettoyer le cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 8. Démarrer le serveur Laravel
```bash
php artisan serve
```

Le backend sera accessible sur : **http://localhost:8000**

---

## Étape 5 : Installation du Frontend (Nuxt.js)

### 1. Ouvrir un NOUVEAU terminal
Ne fermez pas le terminal du backend !

### 2. Naviguer vers le dossier frontend
```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\frontend
```

### 3. Installer les dépendances Node.js
```bash
npm install
```

**Note** : Cela peut prendre plusieurs minutes.

Si vous rencontrez des erreurs, essayez :
```bash
npm install --force
```

### 4. Configurer le fichier .env
```bash
# Copier le fichier d'exemple (si existe)
copy .env.example .env
```

Ouvrez le fichier `.env` (ou créez-le s'il n'existe pas) :
```env
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
```

### 5. Démarrer le serveur de développement Nuxt
```bash
npm run dev
```

Le frontend sera accessible sur : **http://localhost:3000**

---

## Étape 6 : Créer un Compte Administrateur

### Option A : Via Tinker (recommandé)
```bash
# Dans le terminal backend
php artisan tinker
```

Puis exécutez :
```php
$admin = new App\Models\User();
$admin->name = 'Admin';
$admin->email = 'admin@example.com';
$admin->password = bcrypt('password123');
$admin->role = 'admin';
$admin->save();
exit
```

### Option B : Via un Seeder
Si vous avez un seeder d'utilisateurs :
```bash
php artisan db:seed --class=UserSeeder
```

---

## Étape 7 : Vérification de l'Installation

### 1. Tester le Backend
Ouvrez votre navigateur et allez sur :
- [http://localhost:8000/api/health](http://localhost:8000/api/health) (si route existe)
- Ou [http://localhost:8000](http://localhost:8000)

### 2. Tester le Frontend
- Ouvrez [http://localhost:3000](http://localhost:3000)
- Vous devriez voir la page de connexion
- Connectez-vous avec les identifiants admin créés

### 3. Tester l'upload de fichiers
- Vérifiez que le dossier `backend/storage/app/public` existe
- Vérifiez que le lien symbolique fonctionne : `backend/public/storage` → `backend/storage/app/public`

---

## Structure des Dossiers Importants

```
Gestion_Dignitaire/
├── gestion-dignitaire-v2/
│   ├── backend/                 # Application Laravel
│   │   ├── app/                # Code PHP (Controllers, Models, etc.)
│   │   ├── config/             # Fichiers de configuration
│   │   ├── database/           # Migrations, Seeders
│   │   ├── routes/             # Routes API
│   │   ├── storage/            # Fichiers uploadés
│   │   │   └── app/public/    # Fichiers publics accessibles
│   │   ├── .env               # Configuration environnement
│   │   └── composer.json      # Dépendances PHP
│   │
│   └── frontend/               # Application Nuxt.js
│       ├── components/        # Composants Vue réutilisables
│       ├── pages/             # Pages de l'application
│       ├── plugins/           # Plugins Nuxt
│       ├── layouts/           # Layouts de page
│       ├── .env              # Configuration environnement
│       └── package.json      # Dépendances Node.js
```

---

## Commandes Utiles

### Backend (Laravel)
```bash
# Nettoyer tout le cache
php artisan optimize:clear

# Voir les routes disponibles
php artisan route:list

# Créer une migration
php artisan make:migration create_table_name

# Créer un contrôleur
php artisan make:controller NomController

# Créer un modèle avec migration
php artisan make:model NomModel -m

# Réinitialiser la base de données
php artisan migrate:fresh --seed
```

### Frontend (Nuxt.js)
```bash
# Démarrer en mode développement
npm run dev

# Build pour production
npm run build

# Démarrer en production
npm run start

# Analyser le bundle
npm run analyze
```

---

## Résolution des Problèmes Courants

### 1. Erreur "Class not found"
```bash
cd backend
composer dump-autoload
php artisan config:clear
```

### 2. Erreur "SQLSTATE[HY000] [1045] Access denied"
- Vérifiez les identifiants dans `backend/.env`
- Assurez-vous que MySQL est démarré dans MAMP
- Vérifiez le port MySQL (3306 ou 8889)

### 3. Erreur "storage link already exists"
```bash
# Supprimer le lien existant
rm backend/public/storage
# Recréer le lien
php artisan storage:link
```

### 4. Erreur CORS (Frontend ne peut pas contacter Backend)
Vérifiez dans `backend/config/cors.php` :
```php
'allowed_origins' => ['http://localhost:3000'],
```

### 5. Port déjà utilisé
```bash
# Backend sur un autre port
php artisan serve --port=8001

# Frontend sur un autre port
npm run dev -- --port 3001
```

### 6. Erreur "npm ERR! code ENOENT"
```bash
# Supprimer node_modules et package-lock.json
rm -rf node_modules package-lock.json
# Réinstaller
npm install
```

---

## Mise à Jour du Projet

### Backend
```bash
cd backend
git pull                    # Si vous utilisez Git
composer install           # Mettre à jour les dépendances
php artisan migrate        # Appliquer nouvelles migrations
php artisan config:clear   # Nettoyer le cache
```

### Frontend
```bash
cd frontend
git pull                   # Si vous utilisez Git
npm install               # Mettre à jour les dépendances
# Redémarrer le serveur dev
```

---

## Script d'Installation Automatique (Windows)

Créez un fichier `install.bat` à la racine du projet :

```batch
@echo off
echo ========================================
echo Installation Gestion Dignitaires
echo ========================================
echo.

echo [1/6] Installation Backend...
cd backend
call composer install
copy .env.example .env
call php artisan key:generate
call php artisan storage:link
call php artisan migrate
call php artisan config:clear
call php artisan cache:clear
echo Backend installé!
echo.

echo [2/6] Installation Frontend...
cd ..\frontend
call npm install
echo Frontend installé!
echo.

echo ========================================
echo Installation terminée!
echo ========================================
echo.
echo Pour démarrer l'application:
echo 1. Backend  : cd backend  ^&^& php artisan serve
echo 2. Frontend : cd frontend ^&^& npm run dev
echo.
pause
```

---

## Support

Pour toute question ou problème :
1. Consultez la documentation Laravel : [https://laravel.com/docs](https://laravel.com/docs)
2. Consultez la documentation Nuxt : [https://nuxt.com/docs](https://nuxt.com/docs)
3. Vérifiez les logs :
   - Backend : `backend/storage/logs/laravel.log`
   - Frontend : Console du navigateur (F12)

---

## Informations de Connexion par Défaut

**Administrateur** :
- Email : `admin@example.com`
- Mot de passe : `password123`

**⚠️ Important** : Changez ces identifiants en production !

---

**Version du document** : 1.0  
**Dernière mise à jour** : Juin 2026
