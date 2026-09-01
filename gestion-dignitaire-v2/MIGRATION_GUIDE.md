# Guide de Migration - Base de Données Unifiée

Ce guide explique comment utiliser les migrations Laravel pour créer et peupler la base de données du projet.

---

## Fichiers de Migration Créés

Les anciennes migrations PHP du dossier racine ont été converties en migrations Laravel :

### 1. **2024_01_01_000001_create_base_tables.php**
Crée les tables de base :
- `roles`, `fonctions`, `sousfonctions`
- `users`, `roles_fonctions`, `roles_sousfonctions`
- `user_fonctions`, `user_sousfonctions`
- `domaine`, `langue`, `region`, `pays`, `ville`
- `structure`, `etablissement`, `entite`
- `dignitaire`, `decoration`, `pv`

### 2. **2024_01_01_000002_create_related_tables.php**
Crée les tables relationnelles :
- `diplome`, `enfants`, `langues`
- `experiences`, `postes`
- `nominations`, `historique_nominations`
- `decoration_dignitaire`

### 3. **2024_01_01_000003_add_indexes.php**
Ajoute les index pour améliorer les performances

### 4. **InitialDataSeeder.php**
Peuple la base de données avec les données initiales

---

## Commandes de Migration

### Option 1 : Utiliser le script automatique (Recommandé)
```bash
# Double-cliquez sur le fichier ou exécutez :
migrate-and-seed.bat
```

Ce script va :
1. Supprimer toutes les tables existantes
2. Créer toutes les nouvelles tables
3. Insérer les données initiales
4. Nettoyer le cache

### Option 2 : Commandes manuelles

#### A. Migration Complète (supprime tout et recrée)
```bash
cd backend
php artisan migrate:fresh --seed
```

#### B. Migration avec données (étape par étape)
```bash
cd backend

# 1. Supprimer et recréer les tables
php artisan migrate:fresh

# 2. Insérer les données
php artisan db:seed --class=InitialDataSeeder

# 3. Nettoyer le cache
php artisan config:clear
php artisan cache:clear
```

#### C. Rollback (annuler les migrations)
```bash
cd backend
php artisan migrate:rollback
```

#### D. Vérifier le statut des migrations
```bash
cd backend
php artisan migrate:status
```

---

## Structure de la Base de Données

### Tables Principales
- **users** : Utilisateurs du système
- **dignitaire** : Dignitaires avec leurs informations personnelles
- **decoration** : Décorations disponibles
- **entite** : Entités administratives
- **structure** : Structures organisationnelles
- **etablissement** : Établissements d'enseignement

### Tables de Référence
- **roles** : Rôles des utilisateurs
- **fonctions** : Fonctions/menus principaux
- **sousfonctions** : Sous-fonctions/sous-menus
- **domaine** : Domaines d'études
- **langue** : Langues disponibles
- **region** : Régions du monde
- **pays** : Pays
- **ville** : Villes

### Tables Relationnelles
- **diplome** : Diplômes des dignitaires
- **enfants** : Enfants des dignitaires
- **langues** : Langues parlées par les dignitaires
- **experiences** : Expériences professionnelles
- **postes** : Postes occupés
- **nominations** : Nominations officielles
- **decoration_dignitaire** : Décorations attribuées

### Tables de Gestion
- **pv** : Procès-verbaux
- **historique_nominations** : Historique des nominations

---

## Données Insérées par le Seeder

Le seeder `InitialDataSeeder` insère :

✅ **2 rôles** : Superadmin, Assistant
✅ **7 fonctions** : Gest. Pers., Éduc. & Qualif., Parcours Pro., etc.
✅ **11 sous-fonctions** : Enfant, Dignitaire, Poste, Diplôme, etc.
✅ **5 utilisateurs** avec leurs permissions
✅ **7 domaines** d'études
✅ **7 langues**
✅ **18 régions** du monde
✅ **40 pays**
✅ **40 villes**
✅ **7 structures**
✅ **5 établissements**
✅ **7 entités**
✅ **2 décorations**
✅ **2 PV**
✅ **15 dignitaires** avec leurs données complètes :
  - 5 diplômes
  - 5 enfants
  - 5 langues parlées
  - 5 expériences
  - 15 postes
  - 5 nominations
  - 2 historiques de nominations
  - 2 décorations attribuées

---

## Utilisateurs Créés

| ID | Username | Email | Rôle | Mot de passe (hashé) |
|----|----------|-------|------|---------------------|
| 9 | Magali | devgroupentreprise@gmail.com | Assistant | (hashé) |
| 11 | admin1 | astiger4@gmail.com | **Superadmin** | (hashé) |
| 12 | dorkas | georgeschristian2202@gmail.com | Assistant | (hashé) |
| 14 | Magali dorkas Akanda rut | rapontchombogeorges22@gmail.com | Assistant | (hashé) |
| 16 | tito | tito@gmail.com | **Superadmin** | (hashé) |

**Note** : Les mots de passe sont déjà hashés dans le seeder. Pour créer de nouveaux utilisateurs, utilisez `bcrypt('votre_mot_de_passe')`.

---

## Résolution de Problèmes

### Erreur : "Class 'InitialDataSeeder' not found"
```bash
cd backend
composer dump-autoload
```

### Erreur : "SQLSTATE[42S01]: Base table or view already exists"
```bash
cd backend
php artisan migrate:fresh
```

### Erreur : "Access denied for user 'root'"
Vérifiez dans `backend/.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_dignitaire_v2
DB_USERNAME=root
DB_PASSWORD=root
```

### Voir les erreurs détaillées
```bash
cd backend
php artisan migrate --verbose
```

---

## Différences avec les Anciennes Migrations

### ✅ Avantages des Migrations Laravel

1. **Rollback facile** : `php artisan migrate:rollback`
2. **Suivi des versions** : Sait quelles migrations sont appliquées
3. **Transactions automatiques** : Rollback auto en cas d'erreur
4. **Syntaxe moderne** : Blueprint au lieu de SQL brut
5. **Intégration IDE** : Autocomplétion et vérification de types
6. **Seeders séparés** : Données séparées du schéma
7. **Environnements multiples** : Facile à déployer en staging/production

### 📁 Anciens Fichiers (à conserver en backup)

Les anciens fichiers de migration sont toujours dans :
```
c:\MAMP\htdocs\Gestion_Dignitaire\migrations\
├── 001_create_tables.php
├── 002_create_tables.php
├── 003_insert_data.php
└── 004_add_indexes.php
```

**Ne les supprimez pas** tant que vous n'êtes pas sûr que les nouvelles migrations fonctionnent correctement.

---

## Commandes Utiles

```bash
# Voir toutes les tables créées
php artisan db:show

# Compter les lignes dans une table
php artisan tinker
>>> DB::table('dignitaire')->count();

# Créer une nouvelle migration
php artisan make:migration add_column_to_table

# Créer un nouveau seeder
php artisan make:seeder MySeeder

# Exécuter un seeder spécifique
php artisan db:seed --class=MySeeder
```

---

## Next Steps

Après avoir exécuté les migrations :

1. ✅ Vérifiez que toutes les tables sont créées : `php artisan db:show`
2. ✅ Testez la connexion au backend Laravel : `http://localhost:8000`
3. ✅ Testez la connexion au frontend Nuxt : `http://localhost:3000`
4. ✅ Connectez-vous avec un compte admin (admin1 ou tito)

---

**Date de création** : Juin 2026  
**Auteur** : Migration depuis l'ancien système PHP vers Laravel
