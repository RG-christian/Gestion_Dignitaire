# Requirements Document - Affichage des Images dans les Quiz Côté Collaborateur

## Introduction

Cette fonctionnalité vise à garantir que les images configurées dans les questions de quiz s'affichent correctement pour les collaborateurs lors de la passation des quiz. Le système doit gérer l'affichage des images de questions (imageQuestion) ainsi que les images utilisées dans les types de questions spécifiques (CHOIX_IMAGE, ASSOCIATION_IMAGE).

## Glossaire

- **Quiz_System**: Le système de gestion et de passation des quiz
- **Question**: Une question individuelle dans un quiz
- **Image_Question**: Une image illustrative associée à l'énoncé d'une question
- **Collaborateur**: Un utilisateur qui passe un quiz
- **Admin**: Un utilisateur qui configure les quiz et upload les images
- **Image_URL**: Le chemin d'accès à une image stockée sur le serveur
- **Question_Type**: Le type de question (CHOIX_UNIQUE, CHOIX_IMAGE, ASSOCIATION_IMAGE, etc.)

## Requirements

### Requirement 1: Affichage des Images d'Énoncé

**User Story:** En tant que collaborateur, je veux voir les images associées aux énoncés des questions, afin de mieux comprendre le contexte de la question.

#### Acceptance Criteria

1. WHEN une question contient un champ imageQuestion non vide, THE Quiz_System SHALL afficher l'image au-dessus ou à côté de l'énoncé de la question
2. WHEN l'image est chargée avec succès, THE Quiz_System SHALL afficher l'image avec des dimensions appropriées (largeur maximale de 600px, hauteur automatique)
3. IF l'image ne peut pas être chargée, THEN THE Quiz_System SHALL afficher un placeholder visuel avec un message d'erreur discret
4. THE Quiz_System SHALL préserver le ratio d'aspect original de l'image lors de l'affichage
5. WHEN l'utilisateur clique sur une image d'énoncé, THE Quiz_System SHALL afficher l'image en plein écran dans une modale

### Requirement 2: Affichage des Images pour Questions de Type CHOIX_IMAGE

**User Story:** En tant que collaborateur, je veux voir clairement les options d'images pour les questions de type CHOIX_IMAGE, afin de pouvoir sélectionner ma réponse visuellement.

#### Acceptance Criteria

1. WHEN une question est de type CHOIX_IMAGE, THE Quiz_System SHALL afficher toutes les images d'options dans une grille responsive
2. THE Quiz_System SHALL afficher chaque image d'option avec une taille uniforme (aspect-square)
3. WHEN une image d'option est sélectionnée, THE Quiz_System SHALL afficher un indicateur visuel clair (bordure colorée et icône de validation)
4. THE Quiz_System SHALL charger toutes les images d'options avant d'afficher la question
5. IF une image d'option ne peut pas être chargée, THEN THE Quiz_System SHALL afficher un placeholder avec le label de l'option

### Requirement 3: Affichage des Images pour Questions de Type ASSOCIATION_IMAGE

**User Story:** En tant que collaborateur, je veux voir les paires d'images à associer clairement, afin de pouvoir répondre correctement aux questions d'association.

#### Acceptance Criteria

1. WHEN une question est de type ASSOCIATION_IMAGE, THE Quiz_System SHALL afficher les images de la colonne A avec des identifiants visuels (A, B, C, etc.)
2. THE Quiz_System SHALL afficher les images de la colonne B dans un ordre aléatoire pour chaque tentative
3. WHEN une association est sélectionnée, THE Quiz_System SHALL afficher un indicateur visuel sur l'image sélectionnée
4. THE Quiz_System SHALL maintenir une taille cohérente pour toutes les images d'association (hauteur de 128px)
5. THE Quiz_System SHALL charger toutes les images d'association avant d'afficher la question

### Requirement 4: Gestion des Chemins d'Images

**User Story:** En tant que système, je veux construire correctement les URLs des images, afin que toutes les images soient accessibles depuis le frontend.

#### Acceptance Criteria

1. WHEN le backend retourne un chemin d'image relatif (ex: /uploads/questions/image.jpg), THE Quiz_System SHALL construire l'URL complète en utilisant la base URL de l'API
2. WHEN le backend retourne une URL complète (ex: http://localhost:5000/uploads/questions/image.jpg), THE Quiz_System SHALL utiliser l'URL telle quelle
3. THE Quiz_System SHALL gérer les chemins d'images avec ou sans slash initial
4. THE Quiz_System SHALL valider que les URLs d'images sont bien formées avant de tenter le chargement
5. IF une URL d'image est invalide ou vide, THEN THE Quiz_System SHALL ne pas tenter de charger l'image

### Requirement 5: Performance et Optimisation

**User Story:** En tant que collaborateur, je veux que les images se chargent rapidement, afin de ne pas ralentir ma progression dans le quiz.

#### Acceptance Criteria

1. THE Quiz_System SHALL précharger les images de la question suivante en arrière-plan
2. WHEN plusieurs images doivent être chargées, THE Quiz_System SHALL les charger en parallèle
3. THE Quiz_System SHALL afficher un indicateur de chargement pendant que les images sont en cours de téléchargement
4. THE Quiz_System SHALL mettre en cache les images déjà chargées pour éviter les rechargements
5. WHEN toutes les images d'une question sont chargées, THE Quiz_System SHALL permettre la navigation vers cette question

### Requirement 6: Accessibilité des Images

**User Story:** En tant que collaborateur utilisant des technologies d'assistance, je veux que les images aient des descriptions appropriées, afin de pouvoir comprendre leur contenu.

#### Acceptance Criteria

1. THE Quiz_System SHALL ajouter un attribut alt descriptif à chaque image d'énoncé
2. THE Quiz_System SHALL ajouter un attribut alt à chaque image d'option avec le label de l'option
3. WHEN une image d'association est affichée, THE Quiz_System SHALL inclure un attribut alt avec l'identifiant de l'image (ex: "Image A")
4. THE Quiz_System SHALL utiliser des attributs ARIA appropriés pour indiquer l'état de sélection des images
5. THE Quiz_System SHALL permettre la navigation au clavier entre les options d'images

### Requirement 7: Gestion des Erreurs d'Affichage

**User Story:** En tant que collaborateur, je veux être informé clairement si une image ne peut pas être chargée, afin de pouvoir continuer le quiz sans confusion.

#### Acceptance Criteria

1. IF une image d'énoncé ne peut pas être chargée, THEN THE Quiz_System SHALL afficher un message discret indiquant que l'image n'est pas disponible
2. IF une image d'option ne peut pas être chargée, THEN THE Quiz_System SHALL afficher un placeholder avec le texte de l'option
3. THE Quiz_System SHALL logger les erreurs de chargement d'images dans la console pour le débogage
4. THE Quiz_System SHALL permettre au collaborateur de continuer le quiz même si certaines images ne se chargent pas
5. WHEN une erreur de chargement se produit, THE Quiz_System SHALL tenter un rechargement automatique une seule fois

### Requirement 8: Responsive Design

**User Story:** En tant que collaborateur utilisant différents appareils, je veux que les images s'affichent correctement sur tous les écrans, afin de pouvoir passer le quiz sur n'importe quel appareil.

#### Acceptance Criteria

1. THE Quiz_System SHALL adapter la taille des images d'énoncé à la largeur de l'écran sur mobile
2. WHEN l'écran est petit (< 768px), THE Quiz_System SHALL afficher les images d'options en une seule colonne
3. WHEN l'écran est moyen (768px - 1024px), THE Quiz_System SHALL afficher les images d'options en 2 colonnes
4. WHEN l'écran est grand (> 1024px), THE Quiz_System SHALL afficher les images d'options en 3 colonnes
5. THE Quiz_System SHALL maintenir la lisibilité des images sur tous les formats d'écran

### Requirement 9: Intégration avec le Backend

**User Story:** En tant que système, je veux récupérer correctement les données d'images depuis l'API, afin d'afficher les bonnes images pour chaque question.

#### Acceptance Criteria

1. WHEN le Quiz_System charge un quiz, THE Quiz_System SHALL récupérer le champ imageQuestion pour chaque question
2. WHEN le Quiz_System charge une question de type CHOIX_IMAGE, THE Quiz_System SHALL récupérer le tableau optionsImages avec les URLs et labels
3. WHEN le Quiz_System charge une question de type ASSOCIATION_IMAGE, THE Quiz_System SHALL récupérer le tableau pairesImages avec imageA et imageB
4. THE Quiz_System SHALL valider la structure des données d'images reçues de l'API
5. IF les données d'images sont mal formées, THEN THE Quiz_System SHALL logger une erreur et afficher la question sans images

### Requirement 10: Sécurité des Images

**User Story:** En tant que système, je veux m'assurer que seules les images autorisées sont affichées, afin de protéger les utilisateurs contre les contenus malveillants.

#### Acceptance Criteria

1. THE Quiz_System SHALL valider que les URLs d'images proviennent du domaine autorisé (même origine ou domaine configuré)
2. THE Quiz_System SHALL rejeter les URLs d'images contenant des protocoles non sécurisés (javascript:, data:, etc.)
3. THE Quiz_System SHALL utiliser des en-têtes de sécurité appropriés lors du chargement des images
4. THE Quiz_System SHALL appliquer une politique de Content Security Policy (CSP) pour les images
5. THE Quiz_System SHALL sanitiser les attributs alt et title des images pour éviter les injections XSS
