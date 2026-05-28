# Guide Complet des Migrations - Gestion des Dignitaires 🇬🇦

## 📌 Vue d'ensemble

Ce guide explique comment gérer les migrations de la base de données du projet.

---

## 🎯 Solution Recommandée : Migration Consolidée

### Fichier Principal
**`backend/database/migrations/2026_05_21_160000_consolidated_schema_updates.php`**

Cette migration unique contient **TOUTES** les modifications de schéma nécessaires.

### Avantages
✅ Un seul fichier à exécuter  
✅ Vérifie automatiquement si les colonnes existent déjà  
✅ Idéal pour les nouveaux déploiements  
✅ Documentation claire et centralisée  

### Contenu
1. **Ajout de `region_id` à la table `ville`**
   - Permet d'associer une ville à une région
   - Foreign key vers `region.id`
   - Suppression en cascade : SET NULL

2. **Ajout de `continent` à la table `region`**
   - Permet de filtrer les régions par continent
   - Index ajouté pour optimiser les requêtes

---

## 🚀 Déploiement

### Pour un Nouveau Serveur

```bash
# 1. Cloner le projet
git clone <url-du-projet>
cd gestion-dignitaire-v2/backend

# 2. Installer les dépendances
composer install

# 3. Configurer l'environnement
cp .env.example .env
# Éditer .env avec vos paramètres de base de données

# 4. Exécuter les migrations
php artisan migrate

# 5. Vérifier le schéma
php verify_schema.php
```

### Pour un Serveur Existant

Si les migrations individuelles ont déjà été exécutées, la migration consolidée détectera automatiquement les colonnes existantes et ne les recréera pas.

```bash
php artisan migrate
```

---

## 📁 Structure des Fichiers

```
backend/
├── database/
│   └── migrations/
│       ├── README_MIGRATIONS.md              # Documentation détaillée
│       ├── 2026_05_21_160000_consolidated... # ⭐ Migration consolidée
│       ├── 2026_05_21_142543_add_region_id...# Migration individuelle 1
│       └── 2026_05_21_154228_add_continent...# Migration individuelle 2
└── verify_schema.php                         # Script de vérification
```

---

## 🔍 Vérification du Schéma

### Script Automatique

```bash
cd backend
php verify_schema.php
```

**Ce script vérifie :**
- ✅ Existence des tables
- ✅ Existence des colonnes
- ✅ Foreign keys
- ✅ Index
- 📊 Statistiques (nombre d'enregistrements)

### Exemple de Sortie

```
╔════════════════════════════════════════════════════════════╗
║  Vérification du Schéma - Gestion des Dignitaires         ║
╚════════════════════════════════════════════════════════════╝

📋 Vérification de la table 'ville'
   ✓ Table existe
   ✓ Colonne 'region_id' existe

📋 Vérification de la table 'region'
   ✓ Table existe
   ✓ Colonne 'continent' existe

🔗 Vérification des foreign keys
   ✓ Foreign key 'ville.region_id' → region.id

📊 Vérification des index
   ✓ Index sur 'region.continent'

✅ Tout est en ordre !
```

---

## 🛠️ Commandes Utiles

### Vérifier le Statut
```bash
php artisan migrate:status
```

### Exécuter les Migrations
```bash
php artisan migrate
```

### Exécuter une Migration Spécifique
```bash
php artisan migrate --path=database/migrations/2026_05_21_160000_consolidated_schema_updates.php
```

### Annuler la Dernière Migration
```bash
php artisan migrate:rollback
```

### Réinitialiser et Réexécuter
```bash
php artisan migrate:refresh
```

### Réinitialiser Complètement
```bash
php artisan migrate:fresh
# ⚠️ ATTENTION : Supprime toutes les données !
```

---

## 📊 Schéma de la Base de Données

### Tables Modifiées

#### Table `ville`
```sql
CREATE TABLE ville (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    pays_id INT,
    region_id INT,  -- ✨ NOUVEAU
    FOREIGN KEY (region_id) REFERENCES region(id) ON DELETE SET NULL
);
```

#### Table `region`
```sql
CREATE TABLE region (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL UNIQUE,
    continent VARCHAR(100)  -- ✨ NOUVEAU
);
```

### Relations

```
pays
  └─ region_id → region.id

ville
  ├─ pays_id → pays.id
  └─ region_id → region.id  ✨ NOUVEAU

region
  └─ continent (Afrique, Amériques, Asie, Europe, Océanie)
```

---

## 🎓 Bonnes Pratiques

### Pour les Développeurs

1. **Avant de Commencer**
   ```bash
   git pull
   php artisan migrate
   php verify_schema.php
   ```

2. **Créer une Nouvelle Migration**
   ```bash
   php artisan make:migration description_de_la_modification
   ```

3. **Tester la Migration**
   ```bash
   # Tester l'application
   php artisan migrate
   
   # Tester le rollback
   php artisan migrate:rollback
   
   # Réappliquer
   php artisan migrate
   ```

4. **Mettre à Jour la Migration Consolidée**
   - Ajouter la nouvelle modification dans `consolidated_schema_updates.php`
   - Mettre à jour ce guide

### Pour la Production

1. **Backup Obligatoire**
   ```bash
   mysqldump -u user -p database > backup_$(date +%Y%m%d_%H%M%S).sql
   ```

2. **Mode Maintenance**
   ```bash
   php artisan down
   ```

3. **Migration**
   ```bash
   php artisan migrate --force
   ```

4. **Vérification**
   ```bash
   php verify_schema.php
   ```

5. **Retour en Ligne**
   ```bash
   php artisan up
   ```

---

## ⚠️ Dépannage

### Erreur : "Table already exists"

**Solution :** Utiliser la migration consolidée qui vérifie l'existence des colonnes.

### Erreur : "Foreign key constraint fails"

**Solution :**
```bash
# Désactiver temporairement les foreign keys
SET FOREIGN_KEY_CHECKS=0;
# Exécuter la migration
php artisan migrate
# Réactiver les foreign keys
SET FOREIGN_KEY_CHECKS=1;
```

### Erreur : "Column not found"

**Solution :**
```bash
# Vérifier le schéma actuel
php verify_schema.php

# Réexécuter les migrations
php artisan migrate
```

---

## 📞 Support

### Vérifications Rapides

1. **Connexion à la base de données**
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

2. **Liste des tables**
   ```bash
   php artisan tinker
   >>> Schema::getTableListing();
   ```

3. **Colonnes d'une table**
   ```bash
   php artisan tinker
   >>> Schema::getColumnListing('ville');
   ```

---

## 📝 Historique des Modifications

| Date | Migration | Description | Statut |
|------|-----------|-------------|--------|
| 2026-05-21 | `consolidated_schema_updates` | Migration consolidée unique | ✅ Active |
| 2026-05-21 | ~~`add_region_id_to_ville_table`~~ | Ajout de region_id à ville | 🗑️ Supprimée (consolidée) |
| 2026-05-21 | ~~`add_continent_to_region_table`~~ | Ajout de continent à region | 🗑️ Supprimée (consolidée) |

**Note :** Les migrations individuelles ont été supprimées car elles sont maintenant incluses dans la migration consolidée.

---

## ✅ Checklist de Déploiement

- [ ] Backup de la base de données effectué
- [ ] Fichier `.env` configuré correctement
- [ ] Dépendances Composer installées
- [ ] Mode maintenance activé (production)
- [ ] Migrations exécutées : `php artisan migrate`
- [ ] Schéma vérifié : `php verify_schema.php`
- [ ] Tests fonctionnels effectués
- [ ] Mode maintenance désactivé (production)
- [ ] Documentation mise à jour

---

**Dernière mise à jour :** 21 mai 2026  
**Version :** 1.0  
**Projet :** Gestion des Dignitaires - République Gabonaise 🇬🇦
