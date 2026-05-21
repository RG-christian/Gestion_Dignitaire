# Instructions de Restauration 🔧

## ⚠️ IMPORTANT : La base de données a été supprimée

La commande `php artisan migrate:fresh` a supprimé toutes les tables de la base de données.

## Étapes de Restauration

### 1. Restaurer la base de données depuis une sauvegarde

Si vous avez une sauvegarde SQL :
```bash
mysql -u root -p gestion_dignitaire < backup.sql
```

Ou via phpMyAdmin :
1. Ouvrir phpMyAdmin
2. Sélectionner la base `gestion_dignitaire`
3. Onglet "Importer"
4. Choisir votre fichier de sauvegarde
5. Cliquer sur "Exécuter"

### 2. Créer les nouvelles tables pour téléphones et emails

Exécutez ce SQL dans phpMyAdmin ou via MySQL :

```sql
USE gestion_dignitaire;

-- Table des téléphones
CREATE TABLE IF NOT EXISTS `dignitaire_telephones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dignitaire_id` bigint unsigned NOT NULL,
  `numero` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'mobile',
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dignitaire_telephones_dignitaire_id_index` (`dignitaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des emails
CREATE TABLE IF NOT EXISTS `dignitaire_emails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dignitaire_id` bigint unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'personnel',
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dignitaire_emails_dignitaire_id_index` (`dignitaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Ajouter des données de test (optionnel)

```bash
cd gestion-dignitaire-v2/backend
php add_test_data.php
```

Cela ajoutera automatiquement :
- 2 téléphones par dignitaire (mobile + bureau)
- 2 emails par dignitaire (professionnel + personnel)

Pour les 5 premiers dignitaires de la base.

### 4. Vérifier que tout fonctionne

1. Démarrer le backend :
```bash
cd gestion-dignitaire-v2/backend
php artisan serve
```

2. Démarrer le frontend :
```bash
cd gestion-dignitaire-v2/frontend
npm run dev
```

3. Tester :
- Aller sur http://localhost:3000/dignitaires
- Cliquer sur "Voir" pour un dignitaire
- Vérifier que les sections s'affichent :
  - ✅ Informations personnelles
  - ✅ Téléphones (si données ajoutées)
  - ✅ Emails (si données ajoutées)
  - ✅ Postes
  - ✅ Diplômes
  - ✅ Enfants
  - ✅ Décorations

## Alternative : Recréer toute la base

Si vous n'avez pas de sauvegarde, vous pouvez recréer toute la base :

```bash
cd gestion-dignitaire-v2/backend

# Recréer toutes les tables
php artisan migrate:fresh

# Créer les tables téléphones/emails
# Exécuter le SQL ci-dessus dans phpMyAdmin

# Ajouter des données de test
php artisan db:seed  # Si vous avez des seeders
```

## Vérification Rapide

Pour vérifier que les tables existent :

```bash
cd gestion-dignitaire-v2/backend
php artisan tinker
```

Puis dans tinker :
```php
DB::select('SHOW TABLES');
```

Vous devriez voir `dignitaire_telephones` et `dignitaire_emails` dans la liste.

## En Cas de Problème

Si les décorations ne s'affichent toujours pas :
1. Vérifier que la table `decorations` existe (pas `decoration`)
2. Vérifier que les colonnes sont : `nom`, `type`, `niveau`, `grade` (pas `deco_nom`, etc.)
3. Vider le cache Laravel : `php artisan cache:clear`
4. Vider le cache Nuxt : `rm -rf .nuxt` dans le dossier frontend

## Support

Si vous rencontrez des problèmes, vérifiez :
1. Les logs Laravel : `storage/logs/laravel.log`
2. La console du navigateur (F12)
3. Les requêtes réseau dans l'onglet Network des DevTools
