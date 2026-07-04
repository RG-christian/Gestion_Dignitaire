# Document d'Exigences - Correction des Problèmes de Build Docker/TypeScript

## Introduction

Ce document définit les exigences pour corriger les problèmes de build Docker de l'application SING Fusion. L'application se construit actuellement avec succès mais génère de nombreux avertissements TypeScript, une variable d'environnement manquante, et présente des vulnérabilités de sécurité dans les dépendances npm. L'objectif est d'obtenir un build propre, sans erreurs ni avertissements, avec une configuration complète et sécurisée.

## Glossaire

- **Build_System**: Le système de compilation TypeScript et Docker qui transforme le code source en application déployable
- **Type_Checker**: Le compilateur TypeScript qui vérifie la cohérence des types dans le code
- **Docker_Compose**: L'outil d'orchestration qui gère les conteneurs de l'application
- **Environment_Variables**: Les variables de configuration définies dans le fichier .env
- **Dependency_Manager**: Le gestionnaire de paquets npm qui gère les dépendances du projet
- **Controller**: Un composant backend qui gère les requêtes HTTP et la logique métier
- **Service**: Un composant backend qui encapsule la logique métier réutilisable
- **Type_Definition**: Une déclaration TypeScript qui définit la structure d'un objet ou d'une interface
- **Implicit_Any**: Un type TypeScript non spécifié qui est automatiquement inféré comme 'any'
- **Security_Vulnerability**: Une faille de sécurité identifiée dans une dépendance npm

## Exigences

### Exigence 1: Correction des Erreurs de Types Implicites

**User Story:** En tant que développeur, je veux que tous les types TypeScript soient explicitement définis, afin d'éviter les erreurs de type à l'exécution et améliorer la maintenabilité du code.

#### Critères d'Acceptation

1. THE Type_Checker SHALL NOT generate warnings for implicit any types in adminController.ts
2. THE Type_Checker SHALL NOT generate warnings for implicit any types in progressController.ts
3. THE Type_Checker SHALL NOT generate warnings for implicit any types in badgeService.ts
4. WHEN the Build_System compiles the backend code, THE Type_Checker SHALL verify that all array properties have explicit type annotations
5. FOR ALL properties userGrowth, moduleCompletion, quizPerformance, data, topPerformers, strugglingUsers, and titresDePoste, THE Type_Definition SHALL specify the exact structure of array elements

### Exigence 2: Correction des Problèmes de Surcharge JWT

**User Story:** En tant que développeur, je veux que les appels à jwt.sign utilisent la signature correcte, afin d'éviter les erreurs de compilation TypeScript.

#### Critères d'Acceptation

1. WHEN authController.ts calls jwt.sign, THE Controller SHALL use a signature compatible with the @types/jsonwebtoken type definitions
2. THE Type_Checker SHALL NOT generate overload errors for jwt.sign calls in authController.ts at lines 34-35
3. THE Controller SHALL maintain the same functional behavior for JWT token generation after the correction

### Exigence 3: Correction des Propriétés Inexistantes

**User Story:** En tant que développeur, je veux que toutes les propriétés référencées existent dans leurs types respectifs, afin d'éviter les erreurs d'exécution.

#### Critères d'Acceptation

1. WHEN entretienController.ts references creerInstancesPourEmploye, THE Type_Definition SHALL include this property in the service interface
2. WHEN entretienInstanceService.ts references employeId, THE Type_Definition SHALL include this property in the relevant data structure
3. WHEN entretienInstanceService.ts references parcours, THE Type_Definition SHALL include this property in the relevant data structure
4. WHEN questionnaireService.ts references id on a DTO at line 184, THE Type_Definition SHALL include this property in the DTO interface
5. WHEN sessionIntegrationService.ts references StatutSession.EN_PAUSE at line 680, THE Type_Definition SHALL include EN_PAUSE in the StatutSession enum
6. THE Type_Checker SHALL NOT generate "property does not exist" errors for any of these references

### Exigence 4: Correction des Problèmes d'Import de Modules

**User Story:** En tant que développeur, je veux que tous les imports de modules soient valides, afin que le code puisse être compilé et exécuté correctement.

#### Critères d'Acceptation

1. WHEN chatbotIndexService.ts imports a module at line 9, THE Type_Checker SHALL resolve the import successfully
2. WHEN chatbotSearchService.ts references the stopword module, THE Type_Definition SHALL provide correct type information for the fra property
3. WHEN chatbotSearchService.ts calls expressions from stopword, THE Type_Definition SHALL indicate these are callable functions
4. THE Build_System SHALL compile all service files without module resolution errors

### Exigence 5: Configuration de la Variable d'Environnement Manquante

**User Story:** En tant que DevOps, je veux que toutes les variables d'environnement référencées soient définies, afin d'éviter les comportements imprévisibles lors du déploiement.

#### Critères d'Acceptation

1. WHEN Docker_Compose reads the configuration file, THE Environment_Variables SHALL include a definition for the "mb" variable
2. THE Docker_Compose SHALL NOT generate warnings about undefined variables during container startup
3. WHERE the "mb" variable is used in docker-compose.yml, THE Environment_Variables SHALL provide an appropriate default value
4. THE .env file SHALL document the purpose and expected format of the "mb" variable

### Exigence 6: Mise à Jour des Dépendances Vulnérables

**User Story:** En tant que responsable sécurité, je veux que les vulnérabilités npm soient corrigées, afin de réduire les risques de sécurité de l'application.

#### Critères d'Acceptation

1. WHEN Dependency_Manager audits the project, THE Security_Vulnerability count SHALL be reduced to zero critical vulnerabilities
2. WHEN Dependency_Manager audits the project, THE Security_Vulnerability count SHALL be reduced to zero high vulnerabilities
3. WHERE a deprecated package has a maintained alternative, THE Dependency_Manager SHALL use the maintained alternative
4. THE Dependency_Manager SHALL replace whatwg-encoding@2.0.0 with a maintained alternative or remove it if unused
5. THE Dependency_Manager SHALL replace multer@1.4.5-lts.2 with a maintained alternative or upgrade to a stable version
6. WHEN updating dependencies, THE Build_System SHALL verify that all existing functionality remains operational

### Exigence 7: Build Docker Sans Avertissements

**User Story:** En tant que développeur, je veux un build Docker complètement propre, afin de garantir la qualité et la fiabilité du déploiement.

#### Critères d'Acceptation

1. WHEN Docker_Compose builds the application, THE Build_System SHALL complete without TypeScript warnings
2. WHEN Docker_Compose builds the application, THE Build_System SHALL complete without environment variable warnings
3. WHEN Docker_Compose starts the containers, THE Build_System SHALL complete without deprecation warnings
4. THE Build_System SHALL produce a production-ready image with all type checks passing
5. FOR ALL backend TypeScript files, THE Type_Checker SHALL verify strict type compliance when noImplicitAny is enabled

### Exigence 8: Documentation des Corrections

**User Story:** En tant que développeur, je veux une documentation claire des corrections apportées, afin de comprendre les changements et maintenir le code à l'avenir.

#### Critères d'Acceptation

1. FOR ALL corrected type errors, THE Type_Definition SHALL include inline comments explaining the type structure
2. FOR ALL updated dependencies, THE Dependency_Manager SHALL document the reason for the update in package.json or a changelog
3. FOR ALL new environment variables, THE .env.example file SHALL include descriptive comments
4. THE Build_System SHALL maintain a record of all corrections applied to achieve a clean build

### Exigence 9: Validation de la Configuration TypeScript

**User Story:** En tant que développeur, je veux que la configuration TypeScript soit optimale pour la détection d'erreurs, afin de maintenir une haute qualité de code.

#### Critères d'Acceptation

1. THE Type_Checker SHALL enforce noImplicitAny: true in tsconfig.json
2. THE Type_Checker SHALL enforce strict: true in tsconfig.json
3. WHEN the Build_System runs with strict type checking, THE Type_Checker SHALL complete without errors
4. THE Type_Checker SHALL verify that all function return types are explicitly declared where ambiguous
5. THE Type_Checker SHALL verify that all async functions properly handle Promise types

### Exigence 10: Tests de Non-Régression

**User Story:** En tant que développeur, je veux vérifier que les corrections n'introduisent pas de régressions, afin de garantir que l'application fonctionne toujours correctement.

#### Critères d'Acceptation

1. WHEN the corrected code is deployed, THE Build_System SHALL verify that all API endpoints remain functional
2. WHEN the corrected code is deployed, THE Build_System SHALL verify that authentication flows work correctly
3. WHEN the corrected code is deployed, THE Build_System SHALL verify that database operations complete successfully
4. FOR ALL corrected files, THE Build_System SHALL verify that the runtime behavior matches the original behavior
5. WHEN Docker_Compose starts all services, THE Build_System SHALL verify that all health checks pass
