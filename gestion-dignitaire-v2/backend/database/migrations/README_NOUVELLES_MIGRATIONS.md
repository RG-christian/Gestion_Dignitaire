# 🆕 Nouvelles Migrations - Phase 1

**Date** : 17 juin 2026  
**Objectif** : Système de candidatures avec validation admin

---

## 📋 Migrations créées

### 1️⃣ `2026_06_17_100000_create_candidats_table.php`

**Table** : `candidats`  
**Rôle** : Zone tampon pour les préinscriptions avant validation

#### Colonnes principales :
- `statut` : 'en_attente', 'valide', 'refuse'
- `nom`, `prenom`, `date_naissance`, `genre` (obligatoires)
- `nip`, `matricule` (optionnels, uniques)
- `lieu_naissance_id` → FK vers `ville`
- `email` (unique pour connexion)
- `password` (pour que le candidat puisse se connecter)
- `photo`, `cv_path`, `lettre_motivation_path`
- `valide_par` → FK vers `users` (admin qui valide)
- `dignitaire_id` → FK vers `dignitaire` (après validation)

#### Flux :
1. Candidat s'inscrit → `statut = 'en_attente'`
2. Admin valide → `statut = 'valide'` + création dignitaire
3. Admin refuse → `statut = 'refuse'` + `motif_refus`

---

### 2️⃣ `2026_06_17_100001_create_candidat_documents_table.php`

**Table** : `candidat_documents`  
**Rôle** : Stocker les documents joints par les candidats

#### Colonnes principales :
- `candidat_id` → FK vers `candidats` (CASCADE)
- `type_document` : 'diplome', 'attestation', 'casier', 'medical', 'cv', 'lettre', 'autre'
- `nom_fichier` : Nom original
- `chemin_fichier` : Chemin de stockage
- `taille_fichier` : En octets
- `extension` : pdf, jpg, png, etc.
- `description` : Description optionnelle

#### Utilisation :
- Upload multiple de documents lors de la candidature
- Consultation par l'admin avant validation

---

### 3️⃣ `2026_06_17_100002_create_conjoints_table.php`

**Table** : `conjoints`  
**Rôle** : Gérer les conjoints des dignitaires

#### Colonnes principales :
- `dignitaire_id` → FK vers `dignitaire` (CASCADE)
- `nom`, `prenom`, `date_naissance`, `genre`
- `lieu_naissance_id` → FK vers `ville`
- `nationalite_id` → FK vers `pays`
- `profession`, `employeur`
- `date_mariage`, `lieu_mariage`
- `statut` : 'actif', 'divorce', 'veuf', 'separe'
- **`est_militaire`** : boolean (recommandation Marcel)
- **`est_dignitaire`** : boolean (recommandation Marcel)
- `grade_militaire`, `fonction_dignitaire`
- `photo`, `acte_mariage_path`

#### Spécificité :
- Un dignitaire peut avoir plusieurs conjoints (historique)
- Lien avec le statut militaire/dignitaire du conjoint

---

## 🚀 Commandes pour exécuter les migrations

### Avant d'exécuter :
1. **Démarrer MAMP** (MySQL doit être en cours d'exécution)
2. Vérifier que la base `gestion_dignitaire` existe

### Exécuter les migrations :

```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend

# Voir les migrations en attente
php artisan migrate:status

# Exécuter les nouvelles migrations
php artisan migrate

# Ou en mode simulation (voir le SQL sans exécuter)
php artisan migrate --pretend
```

---

## ✅ Vérification après migration

```bash
# Se connecter à MySQL via MAMP ou ligne de commande
mysql -u root -p gestion_dignitaire

# Vérifier que les tables existent
SHOW TABLES;

# Voir la structure des nouvelles tables
DESCRIBE candidats;
DESCRIBE candidat_documents;
DESCRIBE conjoints;
```

---

## 📊 Relations créées

```
candidats
├── lieu_naissance_id → ville
├── ville_residence_id → ville
├── valide_par → users
└── dignitaire_id → dignitaire (après validation)

candidat_documents
└── candidat_id → candidats (CASCADE DELETE)

conjoints
├── dignitaire_id → dignitaire (CASCADE DELETE)
├── lieu_naissance_id → ville
└── nationalite_id → pays
```

---

## 🔄 Rollback (si nécessaire)

```bash
# Annuler la dernière migration
php artisan migrate:rollback

# Annuler toutes les migrations
php artisan migrate:reset

# Tout réinitialiser et relancer
php artisan migrate:fresh
```

---

## 📝 Prochaines étapes

Après avoir exécuté ces migrations :

1. ✅ Créer les modèles Laravel (Candidat, CandidatDocument, Conjoint)
2. ✅ Créer les contrôleurs API
3. ✅ Créer les routes API
4. ✅ Créer les pages frontend (inscription, connexion, dashboard)

---

**Statut** : ✅ Migrations créées, en attente d'exécution

