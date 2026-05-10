# 🎯 Configuration Finale - Gestion Dignitaire V2

## ✅ INSTALLATION TERMINÉE

Toutes les dépendances sont maintenant installées ! Voici les étapes finales pour démarrer l'application.

---

## 📋 ÉTAPE 1 : Configurer la Base de Données

### 1.1 Créer la base de données

**Option A : Via phpMyAdmin**
1. Ouvrir http://localhost/phpMyAdmin
2. Cliquer sur "Nouvelle base de données"
3. Nom : `gestion_dignitaire_v2`
4. Interclassement : `utf8mb4_unicode_ci`
5. Créer

**Option B : Via MySQL CLI**
```bash
mysql -u root -p
CREATE DATABASE gestion_dignitaire_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 1.2 Configurer le fichier .env

Ouvrir `backend\.env` et modifier ces lignes :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_dignitaire_v2
DB_USERNAME=root
DB_PASSWORD=root

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

---

## 📊 ÉTAPE 2 : Exécuter les Migrations

```powershell
cd backend
php artisan migrate
```

Vous devriez voir :
```
Migration table created successfully.
Migrating: 2024_01_01_000001_create_base_tables
Migrated:  2024_01_01_000001_create_base_tables
Migrating: 2024_01_01_000002_create_dignitaires_table
Migrated:  2024_01_01_000002_create_dignitaires_table
Migrating: 2024_01_01_000003_create_related_tables
Migrated:  2024_01_01_000003_create_related_tables
```

---

## 👤 ÉTAPE 3 : Créer un Utilisateur Admin

```powershell
cd backend
php artisan tinker
```

Dans Tinker, tapez ces commandes :

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

Vous devriez voir :
```
= App\Models\User {#...}
```

---

## 🚀 ÉTAPE 4 : Démarrer les Serveurs

### Terminal 1 : Backend Laravel

```powershell
cd backend
php artisan serve
```

Vous devriez voir :
```
INFO  Server running on [http://127.0.0.1:8000].
Press Ctrl+C to stop the server
```

### Terminal 2 : Frontend Nuxt

**Ouvrir un NOUVEAU terminal PowerShell**

```powershell
cd frontend
npm run dev
```

Vous devriez voir :
```
Nuxt 3.x.x with Nitro 2.x.x

  > Local:    http://localhost:3000/
  > Network:  use --host to expose

ℹ Vite client warmed up in Xms
✔ Nuxt Nitro server built in Xms
```

---

## 🌐 ÉTAPE 5 : Accéder à l'Application

1. Ouvrir votre navigateur
2. Aller sur : **http://localhost:3000**
3. Cliquer sur "Se connecter"
4. Entrer :
   - **Username** : `admin`
   - **Password** : `password`
5. Vous devriez voir le dashboard !

---

## 🎨 FONCTIONNALITÉS DISPONIBLES

### Pages Principales
- ✅ **Login** - http://localhost:3000/login
- ✅ **Dashboard** - http://localhost:3000/
- ✅ **Dignitaires** - http://localhost:3000/dignitaires
- ✅ **Nominations** - http://localhost:3000/nominations
- ✅ **Décorations** - http://localhost:3000/decorations

### API Backend
- ✅ **POST** `/api/login` - Authentification
- ✅ **GET** `/api/dignitaires` - Liste des dignitaires
- ✅ **POST** `/api/dignitaires` - Créer un dignitaire
- ✅ **GET** `/api/dignitaires/{id}` - Détails d'un dignitaire
- ✅ **PUT** `/api/dignitaires/{id}` - Modifier un dignitaire
- ✅ **DELETE** `/api/dignitaires/{id}` - Supprimer un dignitaire
- ✅ **GET** `/api/nominations` - Liste des nominations
- ✅ **GET** `/api/decorations` - Liste des décorations
- ✅ **GET** `/api/referentiels/*` - Données de référence

---

## 🐛 DÉPANNAGE

### Erreur : "SQLSTATE[HY000] [1049] Unknown database"

**Solution** : La base de données n'existe pas. Créez-la (ÉTAPE 1.1).

### Erreur : "Connection refused" sur l'API

**Solution** : Le backend ne tourne pas.

```powershell
cd backend
php artisan serve
```

### Erreur CORS

**Solution** : Vérifier la configuration CORS.

```powershell
cd backend
php artisan config:clear
php artisan cache:clear
```

Éditer `backend/config/cors.php` :

```php
'allowed_origins' => ['http://localhost:3000'],
'supports_credentials' => true,
```

### Erreur : "nuxt n'est pas reconnu"

**Solution** : Les dépendances npm ne sont pas installées.

```powershell
cd frontend
npm install
```

### Page blanche ou erreur 404

**Solution** : Vérifier que les deux serveurs tournent.

```powershell
# Terminal 1
cd backend
php artisan serve

# Terminal 2
cd frontend
npm run dev
```

---

## 📝 COMMANDES UTILES

### Backend

```powershell
# Voir les routes
php artisan route:list

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear

# Créer un nouveau model
php artisan make:model NomModel -m

# Créer un nouveau controller
php artisan make:controller NomController
```

### Frontend

```powershell
# Démarrer en mode développement
npm run dev

# Build pour production
npm run build

# Prévisualiser le build
npm run preview
```

---

## 🔐 SÉCURITÉ

### Changer le mot de passe admin

```powershell
cd backend
php artisan tinker
```

```php
$user = App\Models\User::where('username', 'admin')->first();
$user->password = bcrypt('nouveau_mot_de_passe');
$user->save();
exit
```

### Créer un nouvel utilisateur

```powershell
cd backend
php artisan tinker
```

```php
$user = new App\Models\User();
$user->username = 'nom_utilisateur';
$user->nom_complet = 'Nom Complet';
$user->email = 'email@example.com';
$user->password = bcrypt('mot_de_passe');
$user->role_id = 2; // 1 = Admin, 2 = Utilisateur
$user->save();
exit
```

---

## 📊 STRUCTURE DE LA BASE DE DONNÉES

### Tables Principales
- `users` - Utilisateurs du système
- `dignitaires` - Informations des dignitaires
- `nominations` - Nominations des dignitaires
- `decorations` - Décorations reçues
- `diplomes` - Diplômes obtenus
- `experiences` - Expériences professionnelles
- `enfants` - Enfants des dignitaires
- `langues_parlees` - Langues parlées

### Tables de Référence
- `pays` - Liste des pays
- `villes` - Liste des villes
- `regions` - Liste des régions
- `postes` - Liste des postes
- `langues` - Liste des langues
- `domaines` - Domaines d'activité
- `structures` - Structures organisationnelles
- `entites` - Entités administratives

---

## 🎯 PROCHAINES ÉTAPES

1. **Tester toutes les fonctionnalités**
   - Créer un dignitaire
   - Ajouter une nomination
   - Ajouter une décoration

2. **Personnaliser l'application**
   - Modifier les couleurs dans `frontend/assets/css/main.css`
   - Ajouter votre logo
   - Personnaliser les textes

3. **Ajouter des données de référence**
   - Remplir la table `pays`
   - Remplir la table `villes`
   - Remplir la table `postes`

4. **Déployer en production**
   - Configurer un serveur web
   - Configurer la base de données de production
   - Build du frontend : `npm run build`

---

## ✨ FÉLICITATIONS !

Votre application **Gestion Dignitaire V2** est maintenant opérationnelle !

**Identifiants par défaut :**
- Username : `admin`
- Password : `password`

**URLs :**
- Frontend : http://localhost:3000
- Backend API : http://localhost:8000/api

---

## 📞 BESOIN D'AIDE ?

Si vous rencontrez des problèmes, consultez :
- `LISEZ-MOI-DABORD.md` - Guide de démarrage
- `INSTALLATION_MANUELLE.md` - Installation détaillée
- `COMPARAISON_FONCTIONNALITES.md` - Comparaison avec l'ancienne version

**Bon développement ! 🚀**
