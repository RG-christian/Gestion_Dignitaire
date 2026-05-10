# 🚀 Démarrage Rapide

## ✅ État de l'Installation

- ✅ Laravel installé
- ✅ Dépendances Composer installées
- ✅ Fichiers générés copiés (18 Models, 5 Controllers, 3 Migrations)
- ✅ Laravel Sanctum installé
- ⏳ npm install en cours (ou à terminer)

---

## 📋 SI NPM INSTALL N'EST PAS TERMINÉ

Si `npm install` est toujours en cours, **attendez qu'il se termine** ou exécutez :

```powershell
cd frontend
npm install
```

---

## 🎯 DÉMARRAGE EN 5 ÉTAPES

### 1️⃣ Configurer la Base de Données

**Créer la base :**
```bash
mysql -u root -p
CREATE DATABASE gestion_dignitaire_v2;
EXIT;
```

**Éditer `backend\.env` :**
```env
DB_DATABASE=gestion_dignitaire_v2
DB_USERNAME=root
DB_PASSWORD=root
```

### 2️⃣ Exécuter les Migrations

```powershell
cd backend
php artisan migrate
```

### 3️⃣ Créer un Admin

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

### 4️⃣ Démarrer le Backend

```powershell
cd backend
php artisan serve
```

### 5️⃣ Démarrer le Frontend

**Nouveau terminal :**
```powershell
cd frontend
npm run dev
```

---

## 🌐 Accéder à l'Application

**URL :** http://localhost:3000

**Identifiants :**
- Username : `admin`
- Password : `password`

---

## 📚 Documentation Complète

- **CONFIGURATION_FINALE.md** - Guide complet de configuration
- **INSTALLATION_MANUELLE.md** - Installation détaillée
- **COMPARAISON_FONCTIONNALITES.md** - Comparaison avec l'ancienne version

---

## 🆘 Problèmes Courants

### npm install bloqué ?

```powershell
# Arrêter avec Ctrl+C et réessayer
cd frontend
npm install
```

### Erreur de base de données ?

Vérifiez que :
- La base `gestion_dignitaire_v2` existe
- Les identifiants dans `backend\.env` sont corrects
- MySQL est démarré

### Erreur CORS ?

```powershell
cd backend
php artisan config:clear
php artisan cache:clear
```

---

**Bon développement ! 🚀**
