# ⚠️ LISEZ-MOI D'ABORD !

## 🚨 PROBLÈME ACTUEL

Vous avez ces erreurs car **Laravel n'est pas encore installé** dans le dossier `backend/`.

Les fichiers que j'ai générés sont des **templates** qui doivent être copiés dans un projet Laravel complet.

---

## ✅ SOLUTION RAPIDE (2 Options)

### 🎯 OPTION 1 : Script Automatique (RECOMMANDÉ)

```powershell
# Depuis le dossier gestion-dignitaire-v2
.\install-windows.ps1
```

Ce script va :
1. ✅ Installer Laravel
2. ✅ Copier tous les fichiers générés
3. ✅ Configurer la base de données
4. ✅ Installer les dépendances npm
5. ✅ Créer l'utilisateur admin

**Temps estimé : 10-15 minutes**

---

### 📝 OPTION 2 : Installation Manuelle

Suivez le guide détaillé : **`INSTALLATION_MANUELLE.md`**

**Temps estimé : 20-30 minutes**

---

## 📂 STRUCTURE ACTUELLE

```
gestion-dignitaire-v2/
├── backend/                    ❌ Vide (juste les templates)
│   ├── app/Models/            ✅ Models générés
│   ├── app/Http/Controllers/  ✅ Controllers générés
│   └── database/migrations/   ✅ Migrations générées
│
├── frontend/                   ❌ Dépendances non installées
│   ├── pages/                 ✅ Pages générées
│   ├── components/            ✅ Composants générés
│   └── package.json           ✅ Configuration
│
└── Documentation/              ✅ Complète
```

---

## 🎯 CE QU'IL FAUT FAIRE

### ÉTAPE 1 : Installer Laravel

```powershell
# Supprimer le dossier backend actuel
cd gestion-dignitaire-v2
Remove-Item -Recurse -Force backend-temp
Rename-Item backend backend-temp

# Créer un nouveau projet Laravel
composer create-project laravel/laravel backend "^10.0"
```

### ÉTAPE 2 : Copier les Fichiers Générés

```powershell
# Copier les Models
Copy-Item backend-temp\app\Models\* backend\app\Models\ -Recurse -Force

# Copier les Controllers
New-Item -ItemType Directory -Force backend\app\Http\Controllers\Api
Copy-Item backend-temp\app\Http\Controllers\Api\* backend\app\Http\Controllers\Api\ -Recurse -Force

# Copier les Migrations
Copy-Item backend-temp\database\migrations\*.php backend\database\migrations\ -Force

# Copier les Routes
Copy-Item backend-temp\routes\api.php backend\routes\api.php -Force
```

### ÉTAPE 3 : Configurer

```powershell
cd backend

# Copier .env
cp .env.example .env

# Générer la clé
php artisan key:generate

# Éditer .env (ouvrir avec notepad)
notepad .env
```

**Modifier dans .env :**
```env
DB_DATABASE=gestion_dignitaire_v2
DB_USERNAME=root
DB_PASSWORD=root

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

### ÉTAPE 4 : Base de Données

**Via phpMyAdmin :**
1. Ouvrir http://localhost/phpMyAdmin
2. Créer une base : `gestion_dignitaire_v2`

**Ou via MySQL CLI :**
```bash
mysql -u root -p
CREATE DATABASE gestion_dignitaire_v2;
EXIT;
```

### ÉTAPE 5 : Migrations

```powershell
cd backend

# Installer Sanctum
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Exécuter les migrations
php artisan migrate
```

### ÉTAPE 6 : Créer Admin

```powershell
php artisan tinker
```

Dans Tinker :
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

### ÉTAPE 7 : Frontend

```powershell
cd ..\frontend
npm install
cp .env.example .env
```

### ÉTAPE 8 : Démarrer

**Terminal 1 - Backend :**
```powershell
cd backend
php artisan serve
```

**Terminal 2 - Frontend :**
```powershell
cd frontend
npm run dev
```

**Navigateur :**
- Ouvrir http://localhost:3000
- Login : `admin` / `password`

---

## 🆘 BESOIN D'AIDE ?

### Si vous êtes bloqué :

1. **Lisez** `INSTALLATION_MANUELLE.md` pour le guide détaillé
2. **Exécutez** `.\install-windows.ps1` pour l'installation automatique
3. **Consultez** la section DÉPANNAGE dans `INSTALLATION_MANUELLE.md`

---

## 📊 FICHIERS GÉNÉRÉS

J'ai créé **73 fichiers** pour vous :

✅ **16 Models Laravel** - Toutes les entités  
✅ **5 Controllers API** - Toutes les routes  
✅ **3 Migrations** - Toute la base de données  
✅ **5 Pages Nuxt** - Interface complète  
✅ **3 Composants Vue** - UI moderne  
✅ **Documentation complète** - 10+ guides  

**Tout est prêt, il faut juste installer Laravel et les dépendances !**

---

## 🎯 PROCHAINE ÉTAPE

**Choisissez une option :**

### Option A : Automatique (Facile)
```powershell
.\install-windows.ps1
```

### Option B : Manuelle (Contrôle total)
Suivez `INSTALLATION_MANUELLE.md` étape par étape

---

## ✨ APRÈS L'INSTALLATION

Vous aurez une application complète avec :
- ✅ Authentification
- ✅ Dashboard
- ✅ Gestion des dignitaires
- ✅ Gestion des nominations
- ✅ Gestion des décorations
- ✅ API RESTful complète

---

**Commencez maintenant ! 🚀**
