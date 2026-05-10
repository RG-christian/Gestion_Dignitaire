# 🏛️ Gestion Dignitaire V2

Application moderne de gestion des dignitaires de la République Gabonaise.

**Stack Technique :** Laravel 10 + Nuxt 3 + Vue 3 + TailwindCSS + Pinia + MySQL

---

## 📋 Table des Matières

- [État de l'Installation](#-état-de-linstallation)
- [Démarrage Rapide](#-démarrage-rapide)
- [Documentation](#-documentation)
- [Fonctionnalités](#-fonctionnalités)
- [Architecture](#-architecture)
- [Technologies](#-technologies)

---

## ✅ État de l'Installation

### Backend ✅
- ✅ Laravel 10 installé
- ✅ 110 packages Composer
- ✅ Laravel Sanctum configuré
- ✅ 18 Models
- ✅ 5 Controllers API
- ✅ 3 Migrations
- ✅ Routes API

### Frontend ⏳
- ⏳ npm install en cours (ou à terminer)
- ✅ 5 Pages
- ✅ 3 Composants
- ✅ Configuration Nuxt

---

## 🚀 Démarrage Rapide

### 1. Vérifier l'Installation

```powershell
.\verifier-installation.ps1
```

### 2. Si npm install n'est pas terminé

```powershell
cd frontend
npm install
```

### 3. Configurer la Base de Données

```bash
mysql -u root -p
CREATE DATABASE gestion_dignitaire_v2;
EXIT;
```

Éditer `backend\.env` :
```env
DB_DATABASE=gestion_dignitaire_v2
DB_USERNAME=root
DB_PASSWORD=root
```

### 4. Exécuter les Migrations

```powershell
cd backend
php artisan migrate
```

### 5. Créer un Admin

```powershell
php artisan tinker
```

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

### 6. Démarrer les Serveurs

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

### 7. Accéder à l'Application

**URL :** http://localhost:3000

**Identifiants :**
- Username : `admin`
- Password : `password`

---

## 📚 Documentation

### Guides de Démarrage
- **[DEMARRAGE_RAPIDE.md](DEMARRAGE_RAPIDE.md)** - Démarrage en 5 étapes
- **[LISEZ-MOI-DABORD.md](LISEZ-MOI-DABORD.md)** - Vue d'ensemble
- **[INSTALLATION_REUSSIE.md](INSTALLATION_REUSSIE.md)** - État de l'installation

### Guides de Configuration
- **[CONFIGURATION_FINALE.md](CONFIGURATION_FINALE.md)** - Configuration complète
- **[INSTALLATION_MANUELLE.md](INSTALLATION_MANUELLE.md)** - Installation pas à pas
- **[INSTALLATION_ALTERNATIVE.md](INSTALLATION_ALTERNATIVE.md)** - Solutions alternatives

### Guides Techniques
- **[GENERATION_COMPLETE.md](GENERATION_COMPLETE.md)** - Récapitulatif de la génération
- **[COMPARAISON_FONCTIONNALITES.md](COMPARAISON_FONCTIONNALITES.md)** - Ancien vs Nouveau
- **[STRUCTURE_PROJET.md](STRUCTURE_PROJET.md)** - Architecture du projet

---

## ✨ Fonctionnalités

### Authentification
- ✅ Login/Logout
- ✅ Protection des routes
- ✅ Gestion des sessions
- ✅ Tokens Sanctum

### Gestion des Dignitaires
- ✅ Liste complète avec pagination
- ✅ Création de nouveaux dignitaires
- ✅ Modification des informations
- ✅ Suppression
- ✅ Recherche et filtres
- ✅ Export de données

### Gestion des Nominations
- ✅ Liste des nominations
- ✅ Création de nominations
- ✅ Modification
- ✅ Suppression
- ✅ Historique complet
- ✅ Filtres par date, poste, etc.

### Gestion des Décorations
- ✅ Liste des décorations
- ✅ Attribution de décorations
- ✅ Modification
- ✅ Suppression
- ✅ Historique
- ✅ Filtres

### Gestion des Diplômes
- ✅ Liste des diplômes
- ✅ Ajout de diplômes
- ✅ Modification
- ✅ Suppression

### Gestion des Expériences
- ✅ Parcours professionnel
- ✅ Ajout d'expériences
- ✅ Modification
- ✅ Suppression

### Gestion des Enfants
- ✅ Liste des enfants
- ✅ Ajout d'enfants
- ✅ Modification
- ✅ Suppression

### Gestion des Langues
- ✅ Langues parlées
- ✅ Niveau de maîtrise
- ✅ Ajout/Modification/Suppression

### Dashboard
- ✅ Statistiques globales
- ✅ Graphiques
- ✅ Dernières activités
- ✅ Accès rapides

### Référentiels
- ✅ Pays
- ✅ Villes
- ✅ Régions
- ✅ Postes
- ✅ Langues
- ✅ Domaines
- ✅ Structures
- ✅ Entités

---

## 🏗️ Architecture

### Backend (Laravel 10)

```
backend/
├── app/
│   ├── Models/              # 18 Models Eloquent
│   └── Http/
│       └── Controllers/
│           └── Api/         # 5 Controllers API
├── database/
│   └── migrations/          # 3 Migrations
├── routes/
│   └── api.php             # Routes API
└── config/
    └── sanctum.php         # Configuration Sanctum
```

### Frontend (Nuxt 3)

```
frontend/
├── pages/                   # 5 Pages
│   ├── index.vue           # Dashboard
│   ├── login.vue           # Connexion
│   ├── dignitaires/
│   ├── nominations/
│   └── decorations/
├── components/              # 3 Composants
│   ├── DashboardLayout.vue
│   ├── StatCard.vue
│   └── DignitaireCard.vue
├── stores/                  # Store Pinia
│   └── auth.ts
├── middleware/              # Middleware
│   └── auth.ts
└── composables/             # Composables
    └── useApi.ts
```

---

## 🛠️ Technologies

### Backend
- **Laravel 10** - Framework PHP
- **Laravel Sanctum** - Authentification API
- **MySQL** - Base de données
- **Composer** - Gestionnaire de dépendances

### Frontend
- **Nuxt 3** - Framework Vue.js
- **Vue 3** - Framework JavaScript
- **TailwindCSS** - Framework CSS
- **Pinia** - State Management
- **Axios** - Client HTTP

### Outils
- **MAMP** - Serveur local
- **npm** - Gestionnaire de packages
- **Git** - Contrôle de version

---

## 📊 Base de Données

### Tables Principales
- `users` - Utilisateurs
- `dignitaires` - Dignitaires
- `nominations` - Nominations
- `decorations` - Décorations
- `diplomes` - Diplômes
- `experiences` - Expériences
- `enfants` - Enfants
- `langues_parlees` - Langues parlées

### Tables de Référence
- `pays` - Pays
- `villes` - Villes
- `regions` - Régions
- `postes` - Postes
- `langues` - Langues
- `domaines` - Domaines
- `structures` - Structures
- `entites` - Entités

---

## 🔐 Sécurité

- ✅ Authentification Laravel Sanctum
- ✅ Protection CSRF
- ✅ Validation des données
- ✅ Hachage des mots de passe
- ✅ Protection des routes
- ✅ CORS configuré

---

## 🆘 Support

### Problèmes Courants

**npm install bloqué ?**
```powershell
cd frontend
npm cache clean --force
npm install
```

**Erreur de base de données ?**
- Vérifier que MySQL est démarré
- Vérifier les identifiants dans `backend\.env`
- Créer la base de données

**Erreur CORS ?**
```powershell
cd backend
php artisan config:clear
php artisan cache:clear
```

### Documentation
Consultez les fichiers de documentation dans le dossier racine.

---

## 📝 Scripts Disponibles

### Backend
```powershell
php artisan serve          # Démarrer le serveur
php artisan migrate        # Exécuter les migrations
php artisan tinker         # Console interactive
php artisan route:list     # Liste des routes
php artisan cache:clear    # Nettoyer le cache
```

### Frontend
```powershell
npm run dev               # Mode développement
npm run build             # Build production
npm run preview           # Prévisualiser le build
```

---

## 🎯 Roadmap

### Version Actuelle (v2.0)
- ✅ Migration vers Laravel + Nuxt
- ✅ Toutes les fonctionnalités de base
- ✅ Interface moderne
- ✅ API RESTful

### Prochaines Versions
- [ ] Export PDF/Excel
- [ ] Notifications en temps réel
- [ ] Gestion des permissions avancée
- [ ] Historique des modifications
- [ ] Recherche avancée
- [ ] Tableau de bord personnalisable

---

## 👥 Contributeurs

- **Développeur Principal** - Migration et développement

---

## 📄 Licence

Ce projet est développé pour la République Gabonaise.

---

## 🙏 Remerciements

Merci d'utiliser Gestion Dignitaire V2 !

Pour toute question ou problème, consultez la documentation ou créez une issue.

**Bon développement ! 🚀**
