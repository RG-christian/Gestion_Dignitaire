# Correction des Modules Sessions et Entretiens

## 1. Contexte

Les modules de gestion des sessions d'intégration et des entretiens présentent plusieurs problèmes :
- Les sessions utilisent des mois au lieu de semaines
- Les URLs API sont incorrectes (erreurs de connexion)
- Les champs de configuration des entretiens ne sont pas bien configurés

## 2. Problèmes Identifiés

### 2.1 Sessions d'Intégration

**Problème** : Le schéma Prisma utilise `dureeMois` (Int) pour stocker la durée en mois, mais l'utilisateur veut des semaines.

**Erreurs console** :
```
Erreur lors de la récupération des sessions: AxiosError
3000/api/sessions:1 Failed to load resource: net::ERR_CONNECTION_REFUSED
```

**Causes** :
- Variable d'environnement incorrecte : `VITE_API_URL` au lieu de `VITE_API_BASE_URL`
- Schéma base de données utilise `dureeMois` au lieu de `dureeSemaines`

### 2.2 Entretiens

**Problème** : Les champs de configuration ne sont pas bien configurés.

**Erreurs console** :
```
3000/api/admin/users:1 Failed to load resource: net::ERR_CONNECTION_REFUSED
3000/api/entretiens/configurations:1 Failed to load resource: net::ERR_CONNECTION_REFUSED
```

**Causes** :
- Variable d'environnement incorrecte dans les services
- Routes API potentiellement incorrectes

## 3. Exigences Fonctionnelles

### 3.1 Sessions d'Intégration - Durée en Semaines

**User Story** : En tant qu'administrateur, je veux créer des sessions d'intégration avec une durée en semaines (pas en mois) pour plus de précision.

**Critères d'acceptation** :
1. Le schéma Prisma doit utiliser `dureeSemaines` (Int) au lieu de `dureeMois`
2. Le calcul de `dateFinPrevue` doit être basé sur les semaines (dateDebut + dureeSemaines * 7 jours)
3. L'interface utilisateur doit afficher et permettre la saisie en semaines
4. Les sessions existantes doivent être migrées (1 mois = 4 semaines)

### 3.2 Correction des URLs API

**User Story** : En tant que développeur, je veux que tous les services utilisent la bonne variable d'environnement pour éviter les erreurs de connexion.

**Critères d'acceptation** :
1. Tous les services frontend doivent utiliser `VITE_API_BASE_URL`
2. La valeur par défaut doit être `http://localhost:5000/api`
3. Les routes doivent être correctement montées dans le backend

### 3.3 Configuration des Entretiens

**User Story** : En tant qu'administrateur, je veux configurer correctement les entretiens avec tous les champs nécessaires.

**Critères d'acceptation** :
1. Les champs de configuration doivent être clairement définis
2. L'interface doit valider les données avant soumission
3. Les intervieweurs doivent être sélectionnables depuis une liste d'utilisateurs

## 4. Exigences Techniques

### 4.1 Modifications du Schéma Prisma

```prisma
model SessionIntegration {
  // ...
  dureeSemaines           Int                      // Durée en semaines (ex: 4, 8, 12)
  dateFinPrevue           DateTime                 // Calculé à partir de dateDebut + dureeSemaines * 7 jours
  // ...
}
```

### 4.2 Migration de Données

- Créer une migration Prisma pour renommer `dureeMois` en `dureeSemaines`
- Convertir les valeurs existantes : `dureeSemaines = dureeMois * 4`
- Recalculer `dateFinPrevue` pour toutes les sessions actives

### 4.3 Services Frontend

**sessionIntegrationService.js** :
- Remplacer `VITE_API_URL` par `VITE_API_BASE_URL`
- Mettre à jour les méthodes pour utiliser `dureeSemaines`

**entretienService.js** :
- Remplacer `VITE_API_URL` par `VITE_API_BASE_URL`
- Ajouter les méthodes manquantes pour les configurations

### 4.4 Contrôleurs Backend

**sessionIntegrationController.ts** :
- Mettre à jour pour accepter `dureeSemaines`
- Calculer `dateFinPrevue` avec : `dateDebut + (dureeSemaines * 7) jours`

**entretienController.ts** :
- Vérifier que toutes les routes sont correctement définies
- S'assurer que les configurations retournent tous les champs nécessaires

## 5. Tâches d'Implémentation

### 5.1 Backend

1. Modifier le schéma Prisma
   - Renommer `dureeMois` en `dureeSemaines`
   - Mettre à jour les commentaires

2. Créer une migration
   - Renommer la colonne
   - Convertir les valeurs (mois * 4)
   - Recalculer `dateFinPrevue`

3. Mettre à jour les services
   - `sessionIntegrationService.ts` : Utiliser `dureeSemaines`
   - Recalculer `dateFinPrevue` avec semaines

4. Mettre à jour les contrôleurs
   - Accepter `dureeSemaines` dans les requêtes
   - Retourner `dureeSemaines` dans les réponses

### 5.2 Frontend

1. Corriger les variables d'environnement
   - `sessionIntegrationService.js` : Utiliser `VITE_API_BASE_URL`
   - `entretienService.js` : Utiliser `VITE_API_BASE_URL`

2. Mettre à jour les composants
   - `SessionManagement.vue` : Afficher "semaines" au lieu de "mois"
   - Formulaires : Champs pour saisir les semaines

3. Mettre à jour les services
   - Remplacer `dureeMois` par `dureeSemaines` dans toutes les méthodes

### 5.3 Tests

1. Tester la création de sessions avec durée en semaines
2. Vérifier le calcul de `dateFinPrevue`
3. Tester la migration des données existantes
4. Vérifier que toutes les routes API fonctionnent

## 6. Critères de Validation

- ✅ Les sessions peuvent être créées avec une durée en semaines
- ✅ `dateFinPrevue` est correctement calculée (dateDebut + semaines * 7 jours)
- ✅ Les sessions existantes sont migrées sans perte de données
- ✅ Aucune erreur de connexion API (ERR_CONNECTION_REFUSED)
- ✅ Les configurations d'entretien peuvent être créées et modifiées
- ✅ L'interface affiche correctement "semaines" au lieu de "mois"

## 7. Notes Techniques

### Calcul de dateFinPrevue

```typescript
// Avant (avec mois)
const dateFinPrevue = new Date(dateDebut);
dateFinPrevue.setMonth(dateFinPrevue.getMonth() + dureeMois);

// Après (avec semaines)
const dateFinPrevue = new Date(dateDebut);
dateFinPrevue.setDate(dateFinPrevue.getDate() + (dureeSemaines * 7));
```

### Migration SQL

```sql
-- Renommer la colonne
ALTER TABLE "sessions_integration" 
RENAME COLUMN "dureeMois" TO "dureeSemaines";

-- Convertir les valeurs (1 mois = 4 semaines)
UPDATE "sessions_integration" 
SET "dureeSemaines" = "dureeSemaines" * 4;

-- Recalculer dateFinPrevue
UPDATE "sessions_integration" 
SET "dateFinPrevue" = "dateDebut" + (INTERVAL '1 day' * "dureeSemaines" * 7)
WHERE "statut" = 'ACTIVE';
```
