# 🔧 Installation Manuelle - Guide Complet

## ⚠️ PROBLÈMES RENCONTRÉS

Vous avez ces erreurs car :
1. ❌ Laravel n'est pas installé dans `backend/`
2. ❌ Les dépendances npm ne sont pas installées dans `frontend/`

---

## ✅ SOLUTION : Installation Manuelle

### 📦 ÉTAPE 1 : Installer Laravel Backend

```powershell
# 1. Aller dans le dossier parent
cd C:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2

# 2. Supprimer le dossier backend actuel (il est vide)
Remove-Item -Recurse -Force backend

# 3. Créer un nouveau projet Laravel
composer create-project laravel/laravel backend "^10.0"

# 4. Aller dans backend
cd backend

# 5. Copier nos fichiers générés
# IMPORTANT : Copiez manuellement ces fichiers depuis l'ancien dossier backend vers le nouveau :
# - app/Models/*.php (tous les models)
# - app/Http/Controllers/Api/*.php (tous les controllers)
# - database/migrations/*.php (toutes les migrations)
# - routes/api.php
# - .env.example
# - composer.json (fusionner les dépendances)
```

### 📋 ÉTAPE 1.5 : Copier les Fichiers Générés

**Option A : Copie Manuelle (Recommandée)**

1. Ouvrez deux explorateurs Windows
2. Source : `gestion-dignitaire-v2/backend/` (ancien)
3. Destination : Le nouveau dossier Laravel créé

Copiez ces dossiers/fichiers :
- `app/Models/` → Copier tous les fichiers .php
- `app/Http/Controllers/Api/` → Copier tous les fichiers .php
- `database/migrations/` → Copier les 3 fichiers de migration
- `routes/api.php` → Remplacer le fichier existant

**Option B : Script PowerShell**

```powershell
# Depuis gestion-dignitaire-v2/
$source = ".\backend-old"
$dest = ".\backend"

# Renommer l'ancien backend
Rename-Item backend backend-old

# Créer nouveau Laravel
composer create-project laravel/laravel backend "^10.0"

# Copier les fichiers
Copy-Item "$source\app\Models\*" "$dest\app\Models\" -Recurse -Force
Copy-Item "$source\app\Http\Controllers\Api\*" "$dest\app\Http\Controllers\Api\" -Recurse -Force
Copy-Item "$source\database\migrations\*" "$dest\database\migrations\" -Force
Copy-Item "$source\routes\api.php" "$dest\routes\api.php" -Force
```

### 🔧 ÉTAPE 2 : Configurer Laravel

```powershell
cd backend

# 1. Copier .env
cp .env.example .env

# 2. Générer la clé
php artisan key:generate

# 3. Éditer .env avec vos informations
# Ouvrir .env dans un éditeur et modifier :
```

**Contenu de `.env` à modifier :**

```env
APP_NAME="Gestion Dignitaires"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_dignitaire_v2
DB_USERNAME=root
DB_PASSWORD=root

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

### 🗄️ ÉTAPE 3 : Créer la Base de Données

```powershell
# Via MySQL CLI
mysql -u root -p

# Dans MySQL :
CREATE DATABASE gestion_dignitaire_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Ou via phpMyAdmin :**
1. Ouvrir http://localhost/phpMyAdmin
2. Cliquer sur "Nouvelle base de données"
3. Nom : `gestion_dignitaire_v2`
4. Interclassement : `utf8mb4_unicode_ci`
5. Créer

### 🔐 ÉTAPE 4 : Installer Sanctum

```powershell
cd backend

# Installer Sanctum
composer require laravel/sanctum

# Publier la configuration
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Exécuter les migrations
php artisan migrate
```

### 👤 ÉTAPE 5 : Créer un Utilisateur Admin

```powershell
php artisan tinker
```

Dans Tinker, tapez :

```php
$user = new App\Models\User();
$user->username = 'admin';
$user->nom_complet = 'Administrateur';
$user->email = 'admin@example.com';
$user->password = bcrypt('password');
$user->role_id = 1;
$user->save();
exit
```

### ⚙️ ÉTAPE 6 : Configurer CORS

Éditer `backend/config/cors.php` :

```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### 🚀 ÉTAPE 7 : Démarrer le Backend

```powershell
cd backend
php artisan serve
```

✅ **Backend disponible sur http://localhost:8000**

---

## 🎨 FRONTEND NUXT

### 📦 ÉTAPE 8 : Installer les Dépendances

```powershell
# Ouvrir un NOUVEAU terminal
cd C:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\frontend

# Installer les dépendances
npm install
```

### 🔧 ÉTAPE 9 : Configurer l'Environnement

```powershell
# Copier .env
cp .env.example .env
```

Vérifier que `.env` contient :

```env
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
```

### 🚀 ÉTAPE 10 : Démarrer le Frontend

```powershell
npm run dev
```

✅ **Frontend disponible sur http://localhost:3000**

---

## 🧪 ÉTAPE 11 : Tester l'Application

1. Ouvrir http://localhost:3000
2. Cliquer sur "Se connecter"
3. Entrer :
   - **Username** : `admin`
   - **Password** : `password`
4. Vous devriez voir le dashboard !

---

## 🐛 DÉPANNAGE

### Erreur : "Could not open input file: artisan"

**Solution** : Laravel n'est pas installé. Recommencez l'ÉTAPE 1.

### Erreur : "nuxt n'est pas reconnu"

**Solution** : Les dépendances npm ne sont pas installées.

```powershell
cd frontend
npm install
```

### Erreur : "Connection refused" sur l'API

**Solution** : Le backend ne tourne pas.

```powershell
cd backend
php artisan serve
```

### Erreur CORS

**Solution** : Vérifier `backend/config/cors.php` et `backend/.env`

```powershell
cd backend
php artisan config:clear
php artisan cache:clear
```

### Erreur : "SQLSTATE[HY000] [1049] Unknown database"

**Solution** : La base de données n'existe pas. Créez-la (ÉTAPE 3).

### Erreur : "Class 'App\Models\User' not found"

**Solution** : Vérifier que le fichier `app/Models/User.php` existe.

Si non, créez-le :

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'nom_complet',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
```

---

## ✅ CHECKLIST FINALE

Avant de démarrer, vérifiez que :

- [ ] Laravel est installé dans `backend/`
- [ ] Le fichier `backend/artisan` existe
- [ ] La base de données `gestion_dignitaire_v2` existe
- [ ] Le fichier `backend/.env` est configuré
- [ ] Les migrations sont exécutées
- [ ] Un utilisateur admin existe
- [ ] Les dépendances npm sont installées dans `frontend/`
- [ ] Le fichier `frontend/.env` existe
- [ ] Le backend tourne sur http://localhost:8000
- [ ] Le frontend tourne sur http://localhost:3000

---

## 🎯 ALTERNATIVE : Utiliser l'Ancien Projet

Si l'installation est trop compliquée, vous pouvez :

1. **Garder l'ancien projet PHP** pour l'instant
2. **Migrer progressivement** module par module
3. **Utiliser uniquement l'API Laravel** avec l'ancien frontend

---

## 📞 BESOIN D'AIDE ?

Si vous êtes bloqué, dites-moi à quelle étape et je vous aiderai !

**Commencez par l'ÉTAPE 1 et suivez le guide pas à pas.** 🚀
