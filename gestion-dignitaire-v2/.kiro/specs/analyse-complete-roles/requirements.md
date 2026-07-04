# Analyse Complète du Projet - Gestion des Rôles

## Vue d'ensemble

Ce document présente une analyse exhaustive de l'architecture du projet SING Fusion, une plateforme d'intégration et de formation pour les nouveaux collaborateurs. Le système implémente trois niveaux de rôles avec des permissions et fonctionnalités distinctes.

## 1. Architecture Globale

### 1.1 Stack Technique

**Backend:**
- Framework: Express.js + TypeScript
- Base de données: PostgreSQL avec Prisma ORM
- Authentification: JWT (JSON Web Tokens)
- Upload de fichiers: Multer
- Email: Nodemailer

**Frontend:**
- Framework: Vue.js 3 (Composition API)
- Router: Vue Router
- State Management: Vuex
- UI: Tailwind CSS
- Icônes: Lucide Vue

### 1.2 Structure de la Base de Données

Le schéma Prisma définit les entités principales:
- **Utilisateur**: Gestion des comptes avec deux types de rôles
  - `role`: RoleCollaborateur (STAGIAIRE, CONSULTANT, EMPLOYE_CDD, EMPLOYE_CDI)
  - `platformRole`: RoleUtilisateur (COLLABORATEUR, ADMIN, SUPER_ADMIN)
  - `statut`: StatutUtilisateur (EN_ATTENTE, ACTIF, INACTIF)
- **Module**: Contenus de formation organisés hiérarchiquement
- **SousModule**: Sections de contenu (vidéo, PDF, PowerPoint, quiz, checklist)
- **Quiz**: Évaluations avec questions et tentatives
- **Badge**: Système de gamification
- **PosteDeTravail**: Métiers et fonctions
- **AuditLog**: Traçabilité des actions
- **Notification**: Système de notifications

## 2. Rôles et Permissions

### 2.1 SUPER_ADMIN (Super Administrateur)

**Accès:** `/super-admin/*`

**Permissions complètes:**
- Gestion des administrateurs (création, modification, désactivation)
- Accès aux logs d'audit complets
- Gestion des postes de travail
- Toutes les permissions ADMIN
- Vue d'ensemble globale de la plateforme

**Routes Backend:**
```
POST   /api/super-admin/admins              - Créer un administrateur
GET    /api/super-admin/admins              - Lister les administrateurs
PATCH  /api/super-admin/admins/:id/status   - Modifier statut admin
GET    /api/super-admin/audit-logs          - Consulter logs d'audit
GET    /api/super-admin/postes              - Lister postes de travail
```

**Interface Frontend:**
- Dashboard avec statistiques globales
- Gestion des administrateurs (SuperAdminAdminsPage.vue)
- Logs d'audit (AuditLogsPage.vue)
- Accès à toutes les fonctionnalités admin

**Caractéristiques:**
- Peut créer et gérer d'autres administrateurs
- Seul rôle avec accès aux logs d'audit détaillés
- Vue consolidée de toute l'activité plateforme
- Gestion des postes de travail (métiers)

### 2.2 ADMIN (Administrateur)

**Accès:** `/admin/*`

**Permissions:**
- Gestion complète des utilisateurs (validation, modification, suppression)
- Gestion des modules et sous-modules (CRUD)
- Gestion des quiz et questions (CRUD)
- Gestion des badges (CRUD + attribution)
- Gestion des postes de travail
- Statistiques et rapports
- Déblocage de quiz pour utilisateurs

**Routes Backend:**
```
# Utilisateurs
GET    /api/admin/utilisateurs              - Liste tous les utilisateurs
GET    /api/admin/utilisateurs/pending      - Utilisateurs en attente
GET    /api/admin/utilisateurs/:id          - Détail utilisateur
PUT    /api/admin/utilisateurs/:id          - Modifier utilisateur
DELETE /api/admin/utilisateurs/:id          - Supprimer utilisateur
POST   /api/admin/utilisateurs/:id/approve  - Approuver inscription
POST   /api/admin/utilisateurs/:id/reject   - Rejeter inscription

# Dashboard & Stats
GET    /api/admin/dashboard/stats           - Statistiques complètes
GET    /api/admin/dashboard/activity        - Activité récente
GET    /api/admin/dashboard/monthly-stats   - Stats mensuelles
GET    /api/admin/overview                  - Vue d'ensemble

# Modules
POST   /api/admin/modules                   - Créer module
PUT    /api/admin/modules/:id               - Modifier module
DELETE /api/admin/modules/:id               - Supprimer module
POST   /api/admin/modules/:moduleId/sous-modules  - Créer sous-module
PUT    /api/admin/modules/sous-modules/:id  - Modifier sous-module
DELETE /api/admin/modules/sous-modules/:id  - Supprimer sous-module

# Quiz
POST   /api/admin/quiz                      - Créer quiz
PUT    /api/admin/quiz/:id                  - Modifier quiz
DELETE /api/admin/quiz/:id                  - Supprimer quiz
POST   /api/admin/quiz/:quizId/questions    - Créer question
PUT    /api/admin/quiz/questions/:id        - Modifier question
DELETE /api/admin/quiz/questions/:id        - Supprimer question
POST   /api/admin/quiz/:quizId/unlock/:utilisateurId  - Débloquer quiz

# Badges
POST   /api/admin/badges                    - Créer badge
PUT    /api/admin/badges/:id                - Modifier badge
DELETE /api/admin/badges/:id                - Supprimer badge
POST   /api/admin/badges/:badgeId/award/:utilisateurId  - Attribuer badge
DELETE /api/admin/badges/:badgeId/remove/:utilisateurId - Retirer badge

# Postes
GET    /api/admin/postes                    - Lister postes
POST   /api/admin/postes                    - Créer poste
PUT    /api/admin/postes/:id                - Modifier poste
DELETE /api/admin/postes/:id                - Supprimer poste

# Notifications
GET    /api/admin/utilisateurs/:id/notifications  - Notifications utilisateur
POST   /api/admin/notifications             - Créer notification
PATCH  /api/admin/notifications/:id/read    - Marquer comme lu

# Upload
POST   /api/admin/upload/*                  - Upload fichiers
```

**Interface Frontend:**
- Dashboard administrateur (AdminDashboard.vue)
- Gestion utilisateurs (UsersManagement.vue)
  - Liste avec filtres (statut, rôle, recherche, année, trimestre)
  - Validation des inscriptions en attente
  - Modification et suppression
- Gestion modules (ModulesManagement.vue)
- Gestion quiz (QuizManagement.vue)
- Gestion badges (BadgesManagement.vue)
- Gestion postes (PostesManagement.vue)
- Support (SupportPage.vue)
- Détail utilisateur (UserDetailPage.vue)

**Caractéristiques:**
- Workflow de validation des inscriptions
- Statistiques détaillées avec graphiques
- Filtrage avancé (par année, trimestre, poste, statut)
- Système de notifications
- Upload de fichiers (images, documents, vidéos)
- Édition en modal pour tous les contenus

### 2.3 COLLABORATEUR (Utilisateur Standard)

**Accès:** `/dashboard`, `/parcours`, `/references`, `/faq`, `/quiz`, `/profil`

**Permissions:**
- Consultation des modules de formation
- Progression dans les parcours d'intégration
- Passage des quiz
- Consultation des badges obtenus
- Accès aux références et documentation
- Gestion de son profil
- Consultation FAQ

**Routes Backend:**
```
# Profil
GET    /api/user/profile                    - Obtenir profil
PUT    /api/user/profile                    - Modifier profil
GET    /api/user/dashboard                  - Dashboard utilisateur

# Modules
GET    /api/modules                         - Liste modules (filtrés par poste)
GET    /api/modules/:id                     - Détail module
GET    /api/modules/:moduleId/progress/:utilisateurId  - Progression
PUT    /api/modules/:moduleId/progress/:utilisateurId  - Mettre à jour progression

# Quiz
GET    /api/quiz                            - Liste quiz disponibles
GET    /api/quiz/:id                        - Détail quiz
POST   /api/quiz/:id/attempt                - Soumettre tentative
GET    /api/quiz/:id/attempts               - Historique tentatives

# Badges
GET    /api/badges                          - Liste badges
GET    /api/badges/:id                      - Détail badge
GET    /api/badges/user/:utilisateurId      - Badges obtenus

# Références
GET    /api/references                      - Liste références
GET    /api/references/:id                  - Télécharger référence

# FAQ
GET    /api/faq                             - Liste FAQ
GET    /api/faq/category/:category          - FAQ par catégorie
```

**Interface Frontend:**
- Dashboard utilisateur (DashboardPage.vue)
  - Tâches en cours et à venir
  - Pourcentage de complétion
  - Badges obtenus
  - Niveau actuel
- Parcours d'intégration (IntegrationPage.vue)
  - Modules de formation
  - Sous-modules avec contenu multimédia
- Modules spécifiques:
  - FormalitesModule.vue
  - PresentationModule.vue
  - IntegrationMetierModule.vue
  - ConnaissanceDonneesModule.vue
  - RapportEtonnementModule.vue
- Quiz (QuizPage.vue)
- Références (ReferencesPage.vue)
- FAQ (FaqPage.vue)
- Profil (ProfilePage.vue)

**Caractéristiques:**
- Inscription avec validation admin requise
- Progression trackée automatiquement
- Système de niveaux et badges
- Contenu filtré par poste de travail
- Interface intuitive et guidée

## 3. Flux d'Authentification

### 3.1 Inscription et Validation

```
1. Utilisateur s'inscrit (/inscription)
   - Statut: EN_ATTENTE
   - platformRole: COLLABORATEUR
   
2. Notification envoyée aux admins
   - Email automatique
   - Notification in-app
   
3. Admin valide ou rejette
   - Approve: Statut → ACTIF + Email confirmation
   - Reject: Statut → INACTIF + Email rejet
   
4. Utilisateur peut se connecter (si ACTIF)
```

### 3.2 Connexion Multi-Rôles

**Trois points d'entrée distincts:**
- `/connexion` - Collaborateurs
- `/admin/connexion` - Administrateurs
- `/super-admin/connexion` - Super Administrateurs

**Processus:**
```
1. POST /api/auth/login { email, password }
2. Vérification credentials + statut ACTIF
3. Génération JWT avec payload:
   {
     id: string,
     email: string,
     platformRole: 'COLLABORATEUR' | 'ADMIN' | 'SUPER_ADMIN'
   }
4. Stockage local:
   - token
   - user (objet complet)
   - isAuthenticated
   - isAdmin
   - isSuperAdmin
5. Redirection selon platformRole
```

### 3.3 Protection des Routes

**Backend (Middleware):**
```typescript
authMiddleware        // Vérifie JWT valide
adminMiddleware       // Vérifie ADMIN ou SUPER_ADMIN
superAdminMiddleware  // Vérifie SUPER_ADMIN uniquement
```

**Frontend (Navigation Guards):**
```javascript
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('isAuthenticated')
  const isAdmin = localStorage.getItem('isAdmin')
  const isSuperAdmin = localStorage.getItem('isSuperAdmin')
  
  // Logique de redirection selon meta.requiresAuth, meta.requiresAdmin, etc.
})
```

## 4. Fonctionnalités Clés

### 4.1 Système de Modules

**Structure hiérarchique:**
```
Module (ex: "Formalités")
├── SousModule 1 (ex: "Vidéo de présentation")
│   ├── Type: VIDEO
│   ├── URL: /uploads/videos/...
│   └── Progression trackée
├── SousModule 2 (ex: "Documents à remplir")
│   ├── Type: PDF
│   └── Checklist
└── SousModule 3 (ex: "Quiz de validation")
    ├── Type: QUIZ
    └── Lié à un Quiz
```

**Filtrage par poste:**
- Modules généraux (estGeneral: true) → Tous
- Modules spécifiques (titresDePoste: [...]) → Postes ciblés

### 4.2 Système de Quiz

**Types de questions:**
- CHOIX_UNIQUE
- CHOIX_MULTIPLES
- CORRESPONDANCE
- CHOIX_IMAGE
- ASSOCIATION_IMAGE
- VRAI_FAUX

**Fonctionnalités:**
- Questions aléatoires (questionsAleatoires)
- Options aléatoires (optionsAleatoires)
- Tentatives limitées (tentativesMax)
- Temps limité (tempsLimite)
- Seuil de réussite (seuilReussite)
- Déblocage manuel par admin
- Délai de déblocage automatique

### 4.3 Système de Badges

**Attribution:**
- Automatique (selon critères)
- Manuelle (par admin)

**Propriétés:**
- Nom, description, icône
- Points, rareté
- Couleur de fond
- Filtrage par poste

### 4.4 Système d'Audit

**Logs trackés:**
- Connexions (succès/échec)
- Modifications utilisateurs
- Création/modification/suppression contenus
- Attribution badges
- Déblocage quiz

**Informations capturées:**
- utilisateurId
- action
- entite (USER, MODULE, QUIZ, BADGE, etc.)
- entiteId
- details (JSON)
- ipAddress
- userAgent
- timestamp

### 4.5 Système de Notifications

**Types:**
- INFO
- SUCCESS
- WARNING
- ERROR

**Déclencheurs:**
- Nouvelle inscription (→ admins)
- Validation compte (→ utilisateur)
- Attribution badge (→ utilisateur)
- Déblocage quiz (→ utilisateur)
- Notifications personnalisées (admin → utilisateur)

## 5. Statistiques et Rapports

### 5.1 Dashboard Super Admin

**Métriques:**
- Total utilisateurs (actifs, inactifs, en attente)
- Total administrateurs (admins, super admins)
- Total modules, quiz, badges
- Taux de réussite quiz
- Progression globale modules
- Activité récente

### 5.2 Dashboard Admin

**Métriques:**
- Utilisateurs actifs
- Demandes en attente
- Badges attribués
- Quiz complétés
- Progression par module
- Statistiques mensuelles (inscriptions par mois)
- Activité récente

**Filtres avancés:**
- Par année
- Par trimestre (Q1, Q2, Q3, Q4)
- Par statut
- Par rôle
- Par poste
- Recherche textuelle

### 5.3 Dashboard Collaborateur

**Métriques personnelles:**
- Niveau actuel
- Badges obtenus
- Pourcentage de complétion global
- Tâche en cours
- Prochaine tâche
- Deadline

## 6. Gestion des Fichiers

### 6.1 Types de Fichiers

**Uploads organisés par type:**
```
/uploads
├── /avatars        - Photos de profil
├── /documents      - Documents PDF, Word
├── /modules        - Contenus de modules
├── /powerpoints    - Présentations
├── /questions      - Images pour questions
└── /videos         - Vidéos de formation
```

### 6.2 Upload et Sécurité

**Backend:**
- Multer pour gestion upload
- Validation type MIME
- Limitation taille fichiers
- Nommage unique (UUID)

**Frontend:**
- Composant FileUploader.vue
- Prévisualisation
- Barre de progression
- Validation côté client

## 7. Points d'Attention et Améliorations

### 7.1 Sécurité

**Implémenté:**
✅ JWT avec expiration
✅ Hachage bcrypt des mots de passe
✅ Middleware d'authentification
✅ Séparation des rôles
✅ Logs d'audit

**À améliorer:**
⚠️ Rate limiting sur login
⚠️ Refresh tokens
⚠️ HTTPS en production
⚠️ Validation stricte des entrées
⚠️ Protection CSRF

### 7.2 Performance

**À optimiser:**
- Pagination systématique
- Cache des statistiques
- Lazy loading des modules
- Compression des images
- CDN pour fichiers statiques

### 7.3 UX/UI

**Points forts:**
✅ Design cohérent (couleurs SING)
✅ Responsive
✅ Feedback utilisateur (SweetAlert)
✅ Loading states

**À améliorer:**
⚠️ Accessibilité (ARIA labels)
⚠️ Mode sombre
⚠️ Internationalisation
⚠️ Notifications push

## 8. Conclusion

Le projet SING Fusion implémente une architecture solide avec une séparation claire des rôles et des responsabilités. Le système de permissions est bien structuré, permettant une gestion granulaire des accès.

**Forces:**
- Architecture modulaire et scalable
- Séparation claire des rôles
- Système d'audit complet
- Interface intuitive
- Gestion complète du cycle de vie utilisateur

**Axes d'amélioration:**
- Renforcement de la sécurité (rate limiting, refresh tokens)
- Optimisation des performances (cache, pagination)
- Tests automatisés (unitaires, intégration, e2e)
- Documentation API (Swagger/OpenAPI)
- Monitoring et alertes
