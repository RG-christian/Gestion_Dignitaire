# Comment Exécuter la Migration

## ⚠️ IMPORTANT - À Lire Avant de Commencer

Cette migration va :
1. **SUPPRIMER** toutes les tables existantes dans `gestion_dignitaire`
2. **RECRÉER** toutes les tables avec les nouvelles migrations Laravel
3. **INSÉRER** les données de test (15 dignitaires, 5 utilisateurs, etc.)

**⚠️ ATTENTION** : Si vous avez des données importantes dans votre base actuelle, **faites un backup** avant de continuer !

---

## Étape 1 : Backup de la Base de Données (RECOMMANDÉ)

### Option A : Via phpMyAdmin
1. Ouvrez phpMyAdmin : [http://localhost/phpMyAdmin](http://localhost/phpMyAdmin)
2. Sélectionnez la base `gestion_dignitaire`
3. Cliquez sur l'onglet **Exporter**
4. Choisissez **Méthode rapide** et **Format SQL**
5. Cliquez sur **Exécuter**
6. Sauvegardez le fichier `.sql` généré

### Option B : Via ligne de commande
```bash
mysqldump -u root -proot gestion_dignitaire > backup_gestion_dignitaire.sql
```

---

## Étape 2 : Vérifier MAMP

Assurez-vous que **MAMP est démarré** :
- ✅ Serveur Apache : actif (voyant vert)
- ✅ Serveur MySQL : actif (voyant vert)

---

## Étape 3 : Exécuter la Migration

### Option A : Script Automatique (Recommandé)

1. Double-cliquez sur le fichier :
   ```
   c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\migrate-and-seed.bat
   ```

2. Le script va :
   - Supprimer les anciennes tables
   - Créer les nouvelles tables
   - Insérer les données
   - Nettoyer le cache

3. Attendez le message :
   ```
   Migration terminee avec succes!
   ```

### Option B : Commandes Manuelles

```bash
# 1. Aller dans le dossier backend
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend

# 2. Supprimer et recréer toutes les tables
php artisan migrate:fresh

# 3. Insérer les données
php artisan db:seed --class=InitialDataSeeder

# 4. Nettoyer le cache
php artisan config:clear
php artisan cache:clear
```

---

## Étape 4 : Vérifier le Résultat

### Via phpMyAdmin
1. Ouvrez [http://localhost/phpMyAdmin](http://localhost/phpMyAdmin)
2. Sélectionnez la base `gestion_dignitaire`
3. Vérifiez que vous avez **toutes ces tables** :

**Tables Principales :**
- ✅ users
- ✅ dignitaire
- ✅ decoration
- ✅ entite
- ✅ structure
- ✅ etablissement
- ✅ pv

**Tables de Référence :**
- ✅ roles
- ✅ fonctions
- ✅ sousfonctions
- ✅ domaine
- ✅ langue
- ✅ region
- ✅ pays
- ✅ ville

**Tables Relationnelles :**
- ✅ diplome
- ✅ enfants
- ✅ langues
- ✅ experiences
- ✅ postes
- ✅ nominations
- ✅ historique_nominations
- ✅ decoration_dignitaire

**Tables de Gestion :**
- ✅ roles_fonctions
- ✅ roles_sousfonctions
- ✅ user_fonctions
- ✅ user_sousfonctions

**Tables Laravel (Système) :**
- ✅ migrations
- ✅ candidats (si déjà créée)
- ✅ candidat_documents (si déjà créée)
- ✅ candidat_diplomes (si déjà créée)
- ✅ candidat_experiences (si déjà créée)
- ✅ admin_notifications (si déjà créée)

### Via Commande
```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend
php artisan db:show
```

---

## Étape 5 : Vérifier les Données

### Compter les enregistrements
```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend
php artisan tinker
```

Puis dans Tinker :
```php
DB::table('dignitaire')->count();  // Devrait retourner 15
DB::table('users')->count();       // Devrait retourner 5
DB::table('pays')->count();        // Devrait retourner 40
DB::table('ville')->count();       // Devrait retourner 40
exit
```

---

## Étape 6 : Tester l'Application

### 1. Démarrer le Backend Laravel
```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend
php artisan serve
```
Backend accessible sur : **http://localhost:8000**

### 2. Démarrer le Frontend Nuxt (Nouveau Terminal)
```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\frontend
npm run dev
```
Frontend accessible sur : **http://localhost:3000**

### 3. Tester la Connexion

Ouvrez [http://localhost:3000](http://localhost:3000) et connectez-vous avec :

**Compte Superadmin 1 :**
- Email : `astiger4@gmail.com`
- Username : `admin1`

**Compte Superadmin 2 :**
- Email : `tito@gmail.com`
- Username : `tito`

---

## ❌ Que Faire en Cas d'Erreur ?

### Erreur : "SQLSTATE[42S01]: Base table or view already exists"

**Cause** : Des tables existent déjà

**Solution** :
```bash
cd backend
php artisan migrate:fresh --seed
```

### Erreur : "Class 'InitialDataSeeder' not found"

**Cause** : Composer doit recharger les classes

**Solution** :
```bash
cd backend
composer dump-autoload
php artisan db:seed --class=InitialDataSeeder
```

### Erreur : "SQLSTATE[HY000] [1045] Access denied"

**Cause** : Mauvais identifiants de base de données

**Solution** : Vérifiez dans `backend/.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_dignitaire
DB_USERNAME=root
DB_PASSWORD=root
```

### Erreur : "Connection refused"

**Cause** : MySQL n'est pas démarré

**Solution** : Démarrez MAMP et vérifiez que MySQL est actif

### Voir les Erreurs Détaillées
```bash
cd backend
php artisan migrate:fresh --seed --verbose
```

---

## 🔄 Restaurer le Backup (Annuler la Migration)

Si quelque chose ne va pas, vous pouvez restaurer votre backup :

### Via phpMyAdmin
1. Ouvrez phpMyAdmin
2. Sélectionnez `gestion_dignitaire`
3. Cliquez sur **Importer**
4. Choisissez votre fichier `.sql` de backup
5. Cliquez sur **Exécuter**

### Via ligne de commande
```bash
mysql -u root -proot gestion_dignitaire < backup_gestion_dignitaire.sql
```

---

## ✅ Checklist de Migration Réussie

- [ ] MAMP est démarré (Apache + MySQL)
- [ ] Backup de la base créé
- [ ] Migration exécutée sans erreur
- [ ] Tables vérifiées dans phpMyAdmin
- [ ] 15 dignitaires dans la base
- [ ] 5 utilisateurs dans la base
- [ ] Backend Laravel démarre sur port 8000
- [ ] Frontend Nuxt démarre sur port 3000
- [ ] Connexion admin fonctionne

---

## 📊 Données de Test Créées

### Utilisateurs (5)
| ID | Username | Email | Rôle |
|----|----------|-------|------|
| 9 | Magali | devgroupentreprise@gmail.com | Assistant |
| 11 | **admin1** | astiger4@gmail.com | **Superadmin** |
| 12 | dorkas | georgeschristian2202@gmail.com | Assistant |
| 14 | Magali dorkas Akanda rut | rapontchombogeorges22@gmail.com | Assistant |
| 16 | **tito** | tito@gmail.com | **Superadmin** |

### Dignitaires (15)
- BONGO Ali (MAT001)
- ONDO Rose (MAT002)
- NDONG Paul (MAT003)
- MOUSSA Fatou (MAT004)
- MEYE Serge (MAT005)
- ... et 10 autres

### Données de Référence
- 7 domaines d'études
- 7 langues
- 18 régions
- 40 pays
- 40 villes
- 7 structures
- 5 établissements
- 7 entités
- 2 décorations

---

## 📞 Support

Pour toute question :
1. Consultez `MIGRATION_GUIDE.md` pour plus de détails
2. Consultez `INSTALLATION.md` pour l'installation complète
3. Vérifiez les logs : `backend/storage/logs/laravel.log`

---

**Date** : Juin 2026  
**Projet** : Gestion des Dignitaires v2  
**Statut** : Prêt à migrer
