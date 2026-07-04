# 📋 PLANNING D'ACTION - Améliorations & Nouvelles Fonctionnalités

**Date de création** : 17 juin 2026  
**Basé sur** : Compte rendu de réunion + Recommandations Marcel Lawson  
**Objectif** : Intégrer les demandes prioritaires et améliorer l'application

---

## 🎯 PHASE 1 : PRIORITÉ CRITIQUE (Version 1.0) - 2 semaines

### ✅ Déjà réalisé
- [x] Design moderne avec gradient gabonais
- [x] Optimisation des performances (N+1 queries, debounce)
- [x] Composants réutilisables (SearchInput, useDebounce)
- [x] SweetAlert2 pour notifications
- [x] Modal province avec select dynamique

---

### 🔴 BLOC 1 : Traçabilité & Audit (3 jours)

#### 1.1 Table d'audit
**Fichier** : `backend/database/migrations/create_audit_logs_table.php`

```sql
CREATE TABLE audit_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NULL,
    user_name VARCHAR(255),
    action VARCHAR(50), -- 'create', 'update', 'delete'
    model VARCHAR(100), -- 'Dignitaire', 'Nomination', etc.
    model_id BIGINT,
    old_values TEXT NULL, -- JSON
    new_values TEXT NULL, -- JSON
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 1.2 Middleware d'audit
**Fichier** : `backend/app/Http/Middleware/AuditMiddleware.php`

**Actions** :
- Capturer automatiquement toutes les créations
- Capturer toutes les modifications avec anciennes/nouvelles valeurs
- Capturer toutes les suppressions
- Enregistrer l'utilisateur et l'horodatage

#### 1.3 Page d'historique
**Fichier** : `frontend/pages/audit/index.vue`

**Fonctionnalités** :
- Filtrer par utilisateur, action, modèle, date
- Afficher les changements (avant/après)
- Export Excel/PDF
- Design moderne avec gradient gabonais

---

### 🔴 BLOC 2 : Système de Candidatures (5 jours)

#### 2.1 Base de données candidats
**Fichier** : `backend/database/migrations/create_candidats_table.php`

```sql
CREATE TABLE candidats (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    statut ENUM('en_attente', 'valide', 'refuse') DEFAULT 'en_attente',
    
    -- Informations personnelles
    nip VARCHAR(50) NULL,
    matricule VARCHAR(50) NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    lieu_naissance_id BIGINT,
    genre ENUM('M', 'F') NOT NULL,
    etat_civil VARCHAR(50),
    photo VARCHAR(255) NULL,
    
    -- Documents
    cv_path VARCHAR(255) NULL,
    lettre_motivation_path VARCHAR(255) NULL,
    
    -- Traçabilité
    date_candidature TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valide_par BIGINT NULL,
    date_validation TIMESTAMP NULL,
    dignitaire_id BIGINT NULL, -- Lien vers dignitaire si validé
    
    FOREIGN KEY (lieu_naissance_id) REFERENCES ville(id),
    FOREIGN KEY (valide_par) REFERENCES users(id)
);
```

#### 2.2 Table documents candidats
```sql
CREATE TABLE candidat_documents (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    candidat_id BIGINT NOT NULL,
    type_document VARCHAR(100), -- 'diplome', 'attestation', 'casier', 'medical', etc.
    nom_fichier VARCHAR(255),
    chemin_fichier VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (candidat_id) REFERENCES candidats(id) ON DELETE CASCADE
);
```

#### 2.3 API Candidatures
**Fichiers** :
- `backend/app/Http/Controllers/CandidatController.php`
- `backend/routes/api.php`

**Endpoints** :
```php
POST   /api/candidatures          // Créer une candidature
GET    /api/candidatures           // Liste des candidatures
GET    /api/candidatures/{id}      // Détail d'une candidature
POST   /api/candidatures/{id}/documents // Upload document
POST   /api/candidatures/{id}/valider   // Valider et créer dignitaire
POST   /api/candidatures/{id}/refuser   // Refuser une candidature
```

#### 2.4 Pages Frontend
**Fichiers** :
- `frontend/pages/candidatures/index.vue` (Liste)
- `frontend/pages/candidatures/create.vue` (Formulaire public)
- `frontend/pages/candidatures/[id].vue` (Détail/Validation)

**Fonctionnalités** :
- Formulaire public de candidature avec upload multiple
- Page admin pour valider/refuser
- Conversion candidat → dignitaire en un clic
- Design moderne cohérent

---

### 🔴 BLOC 3 : Gestion des Conjoints (2 jours)

#### 3.1 Table conjoints
**Fichier** : `backend/database/migrations/create_conjoints_table.php`

```sql
CREATE TABLE conjoints (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id BIGINT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NULL,
    lieu_naissance_id BIGINT NULL,
    profession VARCHAR(255) NULL,
    date_mariage DATE NULL,
    statut ENUM('actif', 'divorce', 'veuf') DEFAULT 'actif',
    est_militaire BOOLEAN DEFAULT FALSE,
    est_dignitaire BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (dignitaire_id) REFERENCES dignitaire(id) ON DELETE CASCADE,
    FOREIGN KEY (lieu_naissance_id) REFERENCES ville(id)
);
```

#### 3.2 API Conjoints
**Fichier** : `backend/app/Http/Controllers/ConjointController.php`

**Endpoints** :
```php
POST   /api/dignitaires/{id}/conjoints
GET    /api/dignitaires/{id}/conjoints
PUT    /api/conjoints/{id}
DELETE /api/conjoints/{id}
```

#### 3.3 Intégration Frontend
**Fichiers à modifier** :
- `frontend/pages/dignitaires/[id].vue` (Ajouter onglet Conjoints)

---

### 🔴 BLOC 4 : Amélioration des Nominations (3 jours)

#### 4.1 Améliorer la table nominations
**Fichier** : `backend/database/migrations/update_nominations_table.php`

```sql
ALTER TABLE nominations ADD COLUMN statut ENUM('en_cours', 'terminee', 'suspendue') DEFAULT 'en_cours';
ALTER TABLE nominations ADD COLUMN date_fin DATE NULL;
ALTER TABLE nominations ADD COLUMN motif_fin VARCHAR(255) NULL;
ALTER TABLE nominations ADD COLUMN remis_disposition BOOLEAN DEFAULT FALSE;
```

#### 4.2 Logique de clôture automatique/manuelle
**Fichier** : `backend/app/Http/Controllers/NominationController.php`

**Fonctionnalités** :
- Lors d'une nouvelle nomination, demander si l'ancienne prend fin
- Modal de confirmation avec date de fin
- Option "Remis à disposition" vs "Fin de fonction"
- Historisation complète

#### 4.3 Frontend Nominations
**Fichier** : `frontend/pages/nominations/index.vue`

**Améliorations** :
- Badge "En cours" / "Terminée"
- Modal de clôture de nomination
- Timeline des nominations par dignitaire
- Statistiques des nominations actives

---

### 🔴 BLOC 5 : Droits d'Accès & Rôles (2 jours)

#### 5.1 Table permissions
**Fichier** : `backend/database/migrations/create_permissions_table.php`

```sql
CREATE TABLE roles (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) UNIQUE, -- 'super_admin', 'admin', 'gestionnaire', 'assistant'
    description TEXT
);

CREATE TABLE permissions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE, -- 'dignitaire.create', 'dignitaire.update', etc.
    description TEXT
);

CREATE TABLE role_permissions (
    role_id BIGINT,
    permission_id BIGINT,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

ALTER TABLE users ADD COLUMN role_id BIGINT;
ALTER TABLE users ADD FOREIGN KEY (role_id) REFERENCES roles(id);
```

#### 5.2 Middleware de permissions
**Fichier** : `backend/app/Http/Middleware/CheckPermission.php`

#### 5.3 Seeder rôles par défaut
**Fichier** : `backend/database/seeders/RolesPermissionsSeeder.php`

**Rôles** :
- **Super Admin** : Tous les droits
- **Admin** : Gestion complète sauf utilisateurs
- **Gestionnaire** : CRUD dignitaires, nominations
- **Assistant** : Lecture seule ou modification limitée

---

## 🎯 PHASE 2 : IMPORTANT (Version 1.1) - 2 semaines

### 🟠 BLOC 6 : Gestion Documentaire (4 jours)

#### 6.1 Système de documents
**Fichier** : `backend/database/migrations/create_documents_table.php`

```sql
CREATE TABLE documents (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id BIGINT NOT NULL,
    type_document VARCHAR(100), -- 'diplome', 'passeport', 'casier', 'medical', 'attestation'
    nom_document VARCHAR(255),
    numero_document VARCHAR(100) NULL,
    date_emission DATE NULL,
    date_expiration DATE NULL,
    organisme_emetteur VARCHAR(255) NULL,
    fichier_path VARCHAR(255),
    statut ENUM('valide', 'expire', 'en_attente') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (dignitaire_id) REFERENCES dignitaire(id) ON DELETE CASCADE
);
```

#### 6.2 Upload & Gestion fichiers
**Fichiers** :
- `backend/app/Http/Controllers/DocumentController.php`
- `frontend/pages/dignitaires/[id]/documents.vue`

**Fonctionnalités** :
- Upload multiple (drag & drop)
- Prévisualisation PDF
- Classement par type et année
- Alertes d'expiration (passeport, certificat médical)

---

### 🟠 BLOC 7 : Page d'Accueil Publique (2 jours)

#### 7.1 Landing page
**Fichier** : `frontend/pages/index.vue`

**Sections** :
- Hero avec gradient gabonais
- Présentation de la plateforme
- Statistiques publiques
- Lien "Déposer une candidature"
- Lien "Espace connexion"
- Footer avec mentions légales

---

### 🟠 BLOC 8 : Amélioration Diplômes & Décorations (3 jours)

#### 8.1 Diplômes avec documents
**Table** : Ajouter `document_path` à la table `diplomes`

#### 8.2 Décorations liées au poste
**Fichier** : `backend/database/migrations/update_decorations_table.php`

```sql
ALTER TABLE decorations ADD COLUMN nomination_id BIGINT NULL;
ALTER TABLE decorations ADD FOREIGN KEY (nomination_id) REFERENCES nominations(id);
```

#### 8.3 Listes déroulantes métier
**Fichiers** :
- Créer tables de référence pour types de décorations
- Créer tables de référence pour grades
- Utiliser des selects au lieu d'inputs libres

---

### 🟠 BLOC 9 : Distinction Nationalité/Affectation (2 jours)

#### 9.1 Table affectations
**Fichier** : `backend/database/migrations/create_affectations_table.php`

```sql
CREATE TABLE affectations (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    dignitaire_id BIGINT NOT NULL,
    pays_id BIGINT NOT NULL,
    ville_id BIGINT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NULL,
    type_affectation VARCHAR(100), -- 'ambassade', 'consulat', 'mission', etc.
    statut ENUM('en_cours', 'terminee') DEFAULT 'en_cours',
    
    FOREIGN KEY (dignitaire_id) REFERENCES dignitaire(id) ON DELETE CASCADE,
    FOREIGN KEY (pays_id) REFERENCES pays(id)
);
```

#### 9.2 Ajouter nationalité distincte
```sql
ALTER TABLE dignitaire ADD COLUMN nationalite_id BIGINT NULL;
ALTER TABLE dignitaire ADD FOREIGN KEY (nationalite_id) REFERENCES pays(id);
```

---

## 🎯 PHASE 3 : FONCTIONNALITÉS FUTURES (Version 2.0) - 3 semaines

### 🟡 BLOC 10 : Notifications & Alertes (5 jours)

#### 10.1 Système de notifications
- Emails automatiques (nominations, validations)
- Alertes de mandat expirant
- Rappels de documents à renouveler
- Notifications temps réel (WebSocket/Pusher)

#### 10.2 Templates d'emails
- Design professionnel aux couleurs gabonaises
- Templates personnalisables

---

### 🟡 BLOC 11 : Rapports & Exports (4 jours)

#### 11.1 Exports avancés
- Export PDF des fiches complètes
- Export Excel avec filtres avancés
- Génération de rapports personnalisés
- Graphiques et statistiques avancées

#### 11.2 Rapports périodiques
- Rapport mensuel automatique
- Rapport trimestriel
- Rapport annuel

---

### 🟡 BLOC 12 : Import Massif (3 jours)

#### 12.1 Import Excel
- Template Excel standardisé
- Validation des données
- Import avec prévisualisation
- Gestion des erreurs détaillée

---

### 🟡 BLOC 13 : Recherche Globale Intelligente (4 jours)

#### 13.1 Moteur de recherche avancé
- Recherche full-text (MySQL ou Elasticsearch)
- Recherche par mots-clés
- Filtres multiples combinés
- Suggestions intelligentes
- Recherche phonétique (noms africains)

---

### 🟡 BLOC 14 : Authentification 2FA (2 jours)

#### 14.1 OTP optionnel
- Activation par utilisateur
- QR Code Google Authenticator
- Codes de secours
- Option SMS (Twilio/Vonage)

---

### 🟡 BLOC 15 : Statistiques Avancées (3 jours)

#### 15.1 Tableaux de bord additionnels
- Répartition géographique interactive (carte)
- Pyramide des âges
- Statistiques par genre, statut, région
- Indicateurs de performance
- Taux de renouvellement

---

## 📊 RÉCAPITULATIF PAR PRIORITÉ

| Phase | Durée | Fonctionnalités clés | Statut |
|-------|-------|---------------------|--------|
| **Phase 1 - V1.0** | **2 semaines** | Traçabilité, Candidatures, Conjoints, Nominations, Droits | 🔴 CRITIQUE |
| **Phase 2 - V1.1** | **2 semaines** | Documents, Page accueil, Diplômes, Affectations | 🟠 IMPORTANT |
| **Phase 3 - V2.0** | **3 semaines** | Notifications, Exports, Import, Recherche, 2FA | 🟡 FUTUR |

---

## 🎯 ORDRE D'IMPLÉMENTATION RECOMMANDÉ

### Semaine 1-2 (Phase 1)
1. ✅ Traçabilité & Audit (BLOC 1) - 3 jours
2. ✅ Système de candidatures (BLOC 2) - 5 jours
3. ✅ Gestion des conjoints (BLOC 3) - 2 jours

### Semaine 3-4 (Phase 1 suite)
4. ✅ Amélioration nominations (BLOC 4) - 3 jours
5. ✅ Droits d'accès & rôles (BLOC 5) - 2 jours
6. 🧪 Tests & corrections - 3 jours
7. 📝 Documentation - 2 jours

### Semaine 5-6 (Phase 2)
8. 🟠 Gestion documentaire (BLOC 6) - 4 jours
9. 🟠 Page d'accueil (BLOC 7) - 2 jours
10. 🟠 Diplômes & décorations (BLOC 8) - 3 jours
11. 🟠 Nationalité/Affectation (BLOC 9) - 2 jours

### Semaine 7-9 (Phase 3)
12. 🟡 Notifications (BLOC 10)
13. 🟡 Rapports & Exports (BLOC 11)
14. 🟡 Import massif (BLOC 12)
15. 🟡 Recherche globale (BLOC 13)
16. 🟡 2FA (BLOC 14)
17. 🟡 Stats avancées (BLOC 15)

---

## 🛠️ STACK TECHNIQUE RECOMMANDÉE

### Backend
- Laravel 10+ avec Sanctum
- MySQL 8.0+
- Redis pour le cache
- Queue pour les jobs asynchrones

### Frontend
- Nuxt 3
- TailwindCSS avec couleurs gabonaises
- SweetAlert2
- Chart.js pour les graphiques

### Services externes
- Mailtrap (dev) / SendGrid (prod) pour emails
- AWS S3 ou local storage pour documents
- Pusher pour notifications temps réel (optionnel)

---

## 📋 CHECKLIST DE VALIDATION

### Phase 1 ✅
- [ ] Audit complet fonctionnel
- [ ] Système de candidatures opérationnel
- [ ] Gestion des conjoints intégrée
- [ ] Nominations avec clôture améliorées
- [ ] Système de permissions complet
- [ ] Tests unitaires & d'intégration

### Phase 2 🟠
- [ ] Upload et gestion documentaire
- [ ] Page d'accueil publique
- [ ] Diplômes avec pièces jointes
- [ ] Distinction nationalité/affectation
- [ ] Documentation utilisateur

### Phase 3 🟡
- [ ] Notifications automatiques
- [ ] Exports PDF/Excel avancés
- [ ] Import Excel
- [ ] Recherche globale
- [ ] 2FA optionnel
- [ ] Statistiques avancées

---

## 📝 NOTES IMPORTANTES

1. **Traçabilité dès V1** : Marcel a insisté sur ce point critique
2. **Candidatures = priorité** : Base tampon avant dignitaire définitif
3. **Droits d'accès stricts** : Différencier assistant/gestionnaire/admin
4. **OTP non imposé** : Rester optionnel tant que non activé
5. **Tests obligatoires** : Chaque fonctionnalité doit être testée
6. **Documentation continue** : Documenter au fur et à mesure

---

## 🚀 DÉMARRAGE

Pour commencer l'implémentation :

```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend

# Créer les migrations
php artisan make:migration create_audit_logs_table
php artisan make:migration create_candidats_table
php artisan make:migration create_candidat_documents_table
php artisan make:migration create_conjoints_table

# Créer les contrôleurs
php artisan make:controller AuditController
php artisan make:controller CandidatController
php artisan make:controller ConjointController

# Créer les modèles
php artisan make:model AuditLog
php artisan make:model Candidat
php artisan make:model Conjoint
```

---

**Prêt à commencer l'implémentation ? Quelle phase souhaitez-vous attaquer en premier ?** 🚀
