# Tâches - Correction Sessions et Entretiens

## Statut Global
- [x] Backend - Schéma Prisma
- [x] Backend - Services
- [x] Backend - Contrôleurs
- [x] Frontend - Services
- [ ] Frontend - Composants Vue
- [ ] Tests

## 1. Backend

### 1.1 Schéma Prisma
- [x] Renommer `dureeMois` en `dureeSemaines`
- [x] Mettre à jour les commentaires
- [x] Appliquer la migration

### 1.2 Services
- [x] Mettre à jour `sessionIntegrationService.ts`
  - [x] Interface `CreerSessionDTO` : `dureeMois` → `dureeSemaines`
  - [x] Calcul de `dateFinPrevue` avec semaines
- [x] Vérifier `entretienConfigurationService.ts`

### 1.3 Contrôleurs
- [x] Mettre à jour `sessionIntegrationController.ts`
  - [x] `creerSession` : Accepter `dureeSemaines`
  - [x] `creerSessionAvecEntretiens` : Accepter `dureeSemaines`

## 2. Frontend

### 2.1 Services
- [x] Corriger `sessionIntegrationService.js`
  - [x] URL API : `VITE_API_URL` → `VITE_API_BASE_URL`
  - [x] Port : `3000` → `5000`
  - [x] Documentation : `dureeMois` → `dureeSemaines`
- [x] Corriger `entretienService.js`
  - [x] URL API : `VITE_API_URL` → `VITE_API_BASE_URL`
  - [x] Port : `3000` → `5000`

### 2.2 Composants Vue
- [ ] Mettre à jour `SessionManagement.vue`
  - [ ] Afficher "semaines" au lieu de "mois"
  - [ ] Formulaire : Champ `dureeSemaines`
  - [ ] Labels : "Durée (semaines)"
  - [ ] Validation : 1-52 semaines
- [ ] Mettre à jour `EntretiensManagement.vue`
  - [ ] Vérifier les champs de configuration
  - [ ] Tester le chargement des données

## 3. Tests

### 3.1 Tests Backend
- [ ] Tester la création de session avec `dureeSemaines`
- [ ] Vérifier le calcul de `dateFinPrevue`
- [ ] Tester les routes API

### 3.2 Tests Frontend
- [ ] Tester l'affichage des sessions
- [ ] Tester la création de session
- [ ] Tester les configurations d'entretien

## 4. Documentation

- [x] Créer `CORRECTIONS_SESSIONS_ENTRETIENS.md`
- [x] Créer `requirements.md`
- [x] Créer `tasks.md`

## 5. Déploiement

- [ ] Redémarrer le serveur backend
- [ ] Vérifier les logs
- [ ] Tester en production
