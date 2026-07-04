# 🚀 Installation et Démarrage Complet

**Date** : 17 juin 2026  
**Version** : Phase 1 - Système de Candidatures

---

## ✅ Ce qui a été créé aujourd'hui

### Backend (Laravel)
- ✅ 3 migrations (candidats, candidat_documents, conjoints)
- ✅ 3 modèles (Candidat, CandidatDocument, Conjoint)
- ✅ 4 contrôleurs API (CandidatAuth, Candidat, CandidatDocument, Conjoint)
- ✅ 27 routes API configurées
- ✅ Documentation API complète

### Frontend (Nuxt 3)
- ✅ Page d'accueil (`/accueil`)
- ✅ Formulaire de candidature (`/candidature`)
- ✅ Page de connexion (`/candidature/login`)
- ✅ Dashboard candidat (`/candidat/dashboard`)
- ✅ 2 plugins (api.js, swal.client.js)

---

## 📋 Prérequis

### Vérifier que tout est installé :

```bash
# PHP (version 8.1+)
php --version

# Composer
composer --version

# Node.js (version 18+)
node --version

# NPM
npm --version

# MySQL (via MAMP ou autre)
mysql --version
```

---

## 🛠️ Installation Étape par Étape

### ÉTAPE 1 : Démarrer MAMP

1. Ouvrir **MAMP**
2. Cliquer sur **Start Servers**
3. Vérifier que MySQL et Apache sont bien démarrés (feux verts)

---

### ÉTAPE 2 : Configurer la Base de Données

#### Option A : Via phpMyAdmin
1. Aller sur `http://localhost/phpMyAdmin` (ou port MAMP)
2. Créer une base de données : `gestion_dignitaire`
3. Collation : `utf8mb4_unicode_ci`

#### Option B : Via Terminal
```bash
mysql -u root -p
CREATE DATABASE gestion_dignitaire CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

---

### ÉTAPE 3 : Backend - Configuration

```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend

# Vérifier le fichier .env
# DB_DATABASE=gestion_dignitaire
# DB_USERNAME=root
# DB_PASSWORD=root (ou votre mot de passe MAMP)
```

**Vérifier le fichier `.env` :**
```env
APP_NAME="Gestion Dignitaires"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_dignitaire
DB_USERNAME=root
DB_PASSWORD=root

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

---

### ÉTAPE 4 : Backend - Exécuter les Migrations

```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend

# Installer les dépendances (si pas déjà fait)
composer install

# Exécuter les migrations
php artisan migrate

# Si erreur "Table already exists", faire :
php artisan migrate:fresh

# Créer le lien symbolique pour le storage
php artisan storage:link
```

**Résultat attendu** :
```
Migration table created successfully.
Migrating: 2027_01_01_000000_create_complete_schema
Migrated:  2027_01_01_000000_create_complete_schema (XXX ms)
Migrating: 2026_06_17_100000_create_candidats_table
Migrated:  2026_06_17_100000_create_candidats_table (XXX ms)
Migrating: 2026_06_17_100001_create_candidat_documents_table
Migrated:  2026_06_17_100001_create_candidat_documents_table (XXX ms)
Migrating: 2026_06_17_100002_create_conjoints_table
Migrated:  2026_06_17_100002_create_conjoints_table (XXX ms)
```

---

### ÉTAPE 5 : Backend - Démarrer le Serveur

```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend

# Démarrer le serveur Laravel
php artisan serve

# Le serveur démarre sur : http://127.0.0.1:8000
```

**⚠️ Laisser ce terminal ouvert !**

---

### ÉTAPE 6 : Frontend - Installation

Ouvrir un **nouveau terminal** :

```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\frontend

# Installer les dépendances
npm install

# Si erreur, essayer :
npm install --legacy-peer-deps
```

---

### ÉTAPE 7 : Frontend - Démarrer le Serveur

```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\frontend

# Démarrer Nuxt
npm run dev

# Le serveur démarre sur : http://localhost:3000
```

**⚠️ Laisser ce terminal ouvert aussi !**

---

## 🧪 Tests Complets

### 1️⃣ Tester l'API Backend

Ouvrir le navigateur : `http://localhost:8000/api/pays`

**Résultat attendu** : Liste des pays en JSON

### 2️⃣ Tester la Page d'Accueil

Ouvrir le navigateur : `http://localhost:3000/accueil`

**Résultat attendu** :
- ✅ Navbar flottante avec logo
- ✅ Hero avec gradient gabonais
- ✅ 6 fonctionnalités avec icônes
- ✅ Processus en 4 étapes
- ✅ Footer

### 3️⃣ Tester l'Inscription

1. Aller sur `http://localhost:3000/accueil`
2. Cliquer sur **"Devenir Dignitaire"**
3. Remplir le formulaire :
   - **Étape 1** : Informations personnelles
   - **Étape 2** : Upload documents (optionnel)
   - **Étape 3** : Confirmation
4. Cliquer sur **"Soumettre ma candidature"**

**Résultat attendu** :
- ✅ Message de succès avec SweetAlert2
- ✅ Redirection vers `/candidat/dashboard`
- ✅ Dashboard avec statut "En attente" ⏳

### 4️⃣ Tester la Connexion

1. Aller sur `http://localhost:3000/candidature/login`
2. Se connecter avec l'email et mot de passe créés
3. Cliquer sur **"Se connecter"**

**Résultat attendu** :
- ✅ Message "Connexion réussie"
- ✅ Redirection vers `/candidat/dashboard`
- ✅ Affichage des informations du candidat

### 5️⃣ Tester le Dashboard

Sur `/candidat/dashboard`, vérifier :
- ✅ Avatar avec initiales
- ✅ Badge de statut (En attente/Validé/Refusé)
- ✅ Informations personnelles affichées
- ✅ Liste des documents uploadés
- ✅ Timeline chronologique
- ✅ Bouton déconnexion fonctionnel

---

## 🔍 Vérification de la Base de Données

### Via phpMyAdmin
1. Aller sur `http://localhost/phpMyAdmin`
2. Sélectionner la base `gestion_dignitaire`
3. Vérifier que ces tables existent :
   - ✅ `candidats`
   - ✅ `candidat_documents`
   - ✅ `conjoints`
   - ✅ `dignitaire` (table principale existante)
   - ✅ `users`
   - ✅ etc.

### Via Terminal
```bash
mysql -u root -p gestion_dignitaire

SHOW TABLES;

# Vérifier la structure
DESCRIBE candidats;
DESCRIBE candidat_documents;
DESCRIBE conjoints;

EXIT;
```

---

## ❌ Résolution des Problèmes Courants

### Problème 1 : "Connection refused" sur l'API

**Cause** : Le backend Laravel n'est pas démarré

**Solution** :
```bash
cd backend
php artisan serve
```

### Problème 2 : "CORS Error" dans le navigateur

**Cause** : Configuration CORS incorrecte

**Solution** : Vérifier `backend/config/cors.php` :
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3000'],
```

### Problème 3 : "Table not found"

**Cause** : Les migrations n'ont pas été exécutées

**Solution** :
```bash
cd backend
php artisan migrate
```

### Problème 4 : "Module not found" dans Nuxt

**Cause** : node_modules manquants

**Solution** :
```bash
cd frontend
npm install
```

### Problème 5 : "$api is not defined"

**Cause** : Le plugin API n'est pas chargé

**Solution** : Vérifier que `frontend/plugins/api.js` existe (✅ créé)

### Problème 6 : "$swal is not defined"

**Cause** : SweetAlert2 non configuré

**Solution** : Vérifier que `frontend/plugins/swal.client.js` existe (✅ créé)

---

## 📂 Structure des Fichiers Créés

```
gestion-dignitaire-v2/
│
├── backend/
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── CandidatAuthController.php ✅
│   │   │   ├── CandidatController.php ✅
│   │   │   ├── CandidatDocumentController.php ✅
│   │   │   └── ConjointController.php ✅
│   │   │
│   │   └── Models/
│   │       ├── Candidat.php ✅
│   │       ├── CandidatDocument.php ✅
│   │       └── Conjoint.php ✅
│   │
│   ├── database/migrations/
│   │   ├── 2026_06_17_100000_create_candidats_table.php ✅
│   │   ├── 2026_06_17_100001_create_candidat_documents_table.php ✅
│   │   └── 2026_06_17_100002_create_conjoints_table.php ✅
│   │
│   └── routes/
│       └── api.php ✅ (modifié avec nouvelles routes)
│
├── frontend/
│   ├── pages/
│   │   ├── accueil.vue ✅
│   │   ├── candidature/
│   │   │   ├── index.vue ✅
│   │   │   └── login.vue ✅
│   │   └── candidat/
│   │       └── dashboard.vue ✅
│   │
│   └── plugins/
│       ├── api.js ✅
│       └── swal.client.js ✅
│
└── Documentation/
    ├── API_CANDIDATS_DOCUMENTATION.md ✅
    ├── PAGES_CANDIDATURE_README.md ✅
    ├── PHASE_1_PROGRESSION.md ✅
    └── INSTALLATION_COMPLETE.md ✅ (ce fichier)
```

---

## ✅ Checklist Finale

Avant de dire que tout est prêt, vérifier :

### Backend
- [ ] MAMP démarré (MySQL + Apache)
- [ ] Base de données `gestion_dignitaire` créée
- [ ] Migrations exécutées avec succès
- [ ] `php artisan serve` en cours d'exécution
- [ ] API accessible sur `http://localhost:8000/api`

### Frontend
- [ ] `npm install` terminé sans erreur
- [ ] `npm run dev` en cours d'exécution
- [ ] Site accessible sur `http://localhost:3000`
- [ ] Page `/accueil` s'affiche correctement

### Fonctionnalités
- [ ] Inscription candidat fonctionne
- [ ] Upload de documents fonctionne
- [ ] Connexion candidat fonctionne
- [ ] Dashboard candidat s'affiche
- [ ] Déconnexion fonctionne

---

## 🎉 Prêt à Utiliser !

Si tous les points de la checklist sont cochés, **tout est prêt** ! 🚀

### Prochaines étapes (Phase 1 - Suite) :

1. ✅ **BLOC 4** : Amélioration des nominations
2. ✅ **BLOC 5** : Système de permissions et rôles
3. ✅ **BLOC 1** : Traçabilité et audit logs
4. ✅ **Page Admin** : Validation des candidatures

---

**Besoin d'aide ?** Relire ce fichier ou consulter la documentation API.

**Statut actuel** : ✅ Backend + Frontend complets pour le système de candidatures
