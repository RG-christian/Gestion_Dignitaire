# Implementation Plan: Affichage des Images dans les Quiz Côté Collaborateur

## Overview

Ce plan d'implémentation structure le développement de la fonctionnalité d'affichage des images dans les quiz en plusieurs phases incrémentales. L'approche privilégie la création des composables de base, puis des composants réutilisables, et enfin l'intégration dans la page existante QuizTakePage.vue.

## Tasks

- [x] 1. Créer les composables de gestion d'images
  - [x] 1.1 Créer le composable useImageUrl.js
    - Implémenter la fonction buildImageUrl() pour construire les URLs complètes
    - Implémenter la fonction isValidImageUrl() pour valider la sécurité des URLs
    - Gérer les chemins relatifs et absolus
    - Rejeter les protocoles non sécurisés (javascript:, data:, file:)
    - _Requirements: 4.1, 4.2, 4.3, 10.1, 10.2_

  - [ ]* 1.2 Écrire les tests unitaires pour useImageUrl
    - Tester buildImageUrl avec chemins relatifs et absolus
    - Tester isValidImageUrl avec URLs valides et invalides
    - Tester la gestion des slashes initiaux
    - _Requirements: 4.1, 4.2, 4.3_

  - [x] 1.3 Créer le composable useImageLoader.js
    - Implémenter le cache global d'images (Map)
    - Implémenter loadImage() avec gestion du cache
    - Implémenter preloadImages() pour chargement parallèle
    - Implémenter la logique de retry (1 tentative après 1 seconde)
    - Implémenter clearCache() et getCacheSize()
    - _Requirements: 5.1, 5.2, 5.4, 7.5_

  - [ ]* 1.4 Écrire les tests unitaires pour useImageLoader
    - Tester le mécanisme de cache
    - Tester le préchargement parallèle
    - Tester la gestion des erreurs et retry
    - _Requirements: 5.4, 7.5_

- [x] 2. Créer le composant QuizQuestionImage.vue
  - [x] 2.1 Implémenter la structure de base du composant
    - Créer le template avec états de chargement, erreur et succès
    - Implémenter les props (imagePath, altText)
    - Utiliser les composables useImageUrl et useImageLoader
    - Ajouter les icônes Lucide (Loader2, ImageOff, Maximize2, X)
    - _Requirements: 1.1, 1.2, 1.3_

  - [x] 2.2 Implémenter la modale plein écran
    - Utiliser Teleport pour afficher la modale au niveau body
    - Implémenter openFullscreen() et closeFullscreen()
    - Ajouter le bouton de fermeture avec icône X
    - Gérer le clic sur l'overlay pour fermer
    - _Requirements: 1.5_

  - [x] 2.3 Ajouter le styling responsive
    - Largeur maximale 600px sur desktop
    - 100% de largeur sur mobile
    - Préserver le ratio d'aspect
    - Ajouter l'overlay "Cliquer pour agrandir"
    - _Requirements: 1.2, 1.4, 8.1_

  - [ ]* 2.4 Écrire les tests unitaires pour QuizQuestionImage
    - Tester l'affichage de l'image
    - Tester l'état de chargement
    - Tester l'état d'erreur avec placeholder
    - Tester l'ouverture/fermeture de la modale
    - _Requirements: 1.1, 1.3, 1.5_

- [ ] 3. Checkpoint - Vérifier le composant QuizQuestionImage
  - Ensure all tests pass, ask the user if questions arise.

- [x] 4. Créer le composant QuizImageOptions.vue
  - [x] 4.1 Implémenter la structure de base
    - Créer le template avec grille d'images
    - Implémenter les props (options, selectedValue)
    - Implémenter l'événement @select
    - Utiliser les composables useImageUrl et useImageLoader
    - _Requirements: 2.1, 2.2_

  - [x] 4.2 Implémenter la grille responsive
    - Calculer gridClass en fonction du nombre d'options
    - 2 colonnes sur mobile
    - 2 colonnes sur tablet
    - 3 colonnes sur desktop
    - Aspect ratio square pour toutes les images
    - _Requirements: 2.1, 8.2, 8.3, 8.4_

  - [x] 4.3 Implémenter la sélection et les indicateurs visuels
    - Ajouter la classe 'selected' avec bordure colorée
    - Afficher l'icône Check sur l'image sélectionnée
    - Gérer le clic pour sélectionner une option
    - Empêcher la sélection si l'image est en erreur
    - _Requirements: 2.3_

  - [x] 4.4 Implémenter le préchargement des images
    - Précharger toutes les images dans onMounted
    - Afficher un loader pendant le préchargement
    - Gérer les erreurs de chargement individuelles
    - Afficher un placeholder avec label si erreur
    - _Requirements: 2.4, 2.5, 5.1, 5.2_

  - [x] 4.5 Ajouter les attributs d'accessibilité
    - Ajouter aria-label sur chaque bouton d'option
    - Ajouter aria-pressed pour indiquer la sélection
    - Ajouter des attributs alt descriptifs sur les images
    - _Requirements: 6.2, 6.4_

  - [ ]* 4.6 Écrire les tests unitaires pour QuizImageOptions
    - Tester l'affichage de la grille
    - Tester la sélection d'une option
    - Tester le préchargement
    - Tester la gestion des erreurs
    - _Requirements: 2.1, 2.3, 2.4_

- [x] 5. Créer le composant QuizImageAssociation.vue
  - [x] 5.1 Implémenter la structure de base
    - Créer le template avec deux colonnes (A et B)
    - Implémenter les props (pairs, selectedAssociations)
    - Implémenter l'événement @update
    - Utiliser les composables useImageUrl et useImageLoader
    - _Requirements: 3.1_

  - [x] 5.2 Implémenter la colonne A (images à associer)
    - Afficher les images avec badges (A, B, C, etc.)
    - Fonction getLetter() pour convertir index en lettre
    - Hauteur fixe de 128px pour toutes les images
    - _Requirements: 3.1, 3.4_

  - [x] 5.3 Implémenter la colonne B (réponses)
    - Mélanger les images B avec shuffleArray()
    - Afficher une grille de 3 options par ligne
    - Implémenter selectAssociation() pour gérer la sélection
    - Afficher l'icône Check sur l'image sélectionnée
    - _Requirements: 3.2, 3.3_

  - [x] 5.4 Implémenter le préchargement des images
    - Précharger toutes les images A et B dans onMounted
    - Afficher un loader pendant le préchargement
    - Gérer les erreurs de chargement
    - _Requirements: 3.5, 5.1, 5.2_

  - [x] 5.5 Ajouter les attributs d'accessibilité
    - Ajouter aria-label sur chaque bouton d'option
    - Ajouter des attributs alt avec identifiants (Image A, Image B, etc.)
    - _Requirements: 6.3, 6.4_

  - [ ]* 5.6 Écrire les tests unitaires pour QuizImageAssociation
    - Tester l'affichage des deux colonnes
    - Tester le mélange des images B
    - Tester la sélection d'associations
    - Tester le préchargement
    - _Requirements: 3.1, 3.2, 3.3_

- [ ] 6. Checkpoint - Vérifier les composants d'images
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 7. Intégrer les composants dans QuizTakePage.vue
  - [x] 7.1 Importer les nouveaux composants
    - Importer QuizQuestionImage, QuizImageOptions, QuizImageAssociation
    - Importer les composables useImageUrl et useImageLoader
    - _Requirements: 9.1_

  - [x] 7.2 Ajouter l'affichage de l'image d'énoncé
    - Ajouter QuizQuestionImage dans l'en-tête de la question
    - Conditionner l'affichage sur currentQuestion.imageQuestion
    - Passer imagePath et altText comme props
    - _Requirements: 1.1, 9.1_

  - [x] 7.3 Remplacer le code CHOIX_IMAGE existant
    - Remplacer le code inline par QuizImageOptions
    - Passer les props options et selectedValue
    - Gérer l'événement @select pour mettre à jour answers
    - _Requirements: 2.1, 2.2, 2.3, 9.2_

  - [x] 7.4 Remplacer le code ASSOCIATION_IMAGE existant
    - Remplacer le code inline par QuizImageAssociation
    - Passer les props pairs et selectedAssociations
    - Gérer l'événement @update pour mettre à jour answers
    - _Requirements: 3.1, 3.2, 3.3, 9.3_

  - [x] 7.5 Implémenter le préchargement de la question suivante
    - Créer une fonction preloadNextQuestion()
    - Appeler preloadNextQuestion() après chaque navigation
    - Précharger les images de currentQuestionIndex + 1
    - _Requirements: 5.1_

  - [x] 7.6 Ajouter la gestion des erreurs globales
    - Wrapper les appels API dans try/catch
    - Afficher un message d'erreur si les données sont mal formées
    - Logger les erreurs dans la console
    - Permettre de continuer le quiz même en cas d'erreur
    - _Requirements: 7.3, 7.4, 9.4, 9.5_

- [ ] 8. Ajouter les styles CSS
  - [ ] 8.1 Créer les styles pour QuizQuestionImage
    - Styles pour .quiz-question-image
    - Styles pour .image-loader et .image-error
    - Styles pour .fullscreen-modal
    - Animations de transition
    - _Requirements: 1.2, 1.5_

  - [ ] 8.2 Créer les styles pour QuizImageOptions
    - Styles pour .quiz-image-options
    - Styles pour .image-grid avec classes responsive
    - Styles pour .image-option et .selected
    - Styles pour .selection-indicator
    - _Requirements: 2.1, 2.3, 8.2, 8.3, 8.4_

  - [ ] 8.3 Créer les styles pour QuizImageAssociation
    - Styles pour .quiz-image-association
    - Styles pour .column-a et .column-b
    - Styles pour .association-image et .image-badge
    - Styles pour .option-grid et .check-overlay
    - _Requirements: 3.1, 3.4_

- [ ] 9. Écrire les tests property-based
  - [ ]* 9.1 Écrire le test de propriété pour URL Construction Consistency
    - **Property 1: URL Construction Consistency**
    - **Validates: Requirements 4.1, 4.2, 4.3**
    - Utiliser fast-check pour générer des chemins aléatoires
    - Vérifier que buildImageUrl() produit toujours une URL valide
    - Minimum 100 itérations

  - [ ]* 9.2 Écrire le test de propriété pour Image Loading Idempotence
    - **Property 2: Image Loading Idempotence**
    - **Validates: Requirements 5.4**
    - Générer des URLs aléatoires
    - Charger la même image plusieurs fois
    - Vérifier qu'une seule requête réseau est faite
    - Minimum 100 itérations

  - [ ]* 9.3 Écrire le test de propriété pour Error Handling Graceful Degradation
    - **Property 3: Error Handling Graceful Degradation**
    - **Validates: Requirements 7.1, 7.2, 7.4**
    - Générer des URLs invalides
    - Vérifier que le système affiche un fallback
    - Vérifier que l'application reste fonctionnelle
    - Minimum 100 itérations

  - [ ]* 9.4 Écrire le test de propriété pour Responsive Grid Adaptation
    - **Property 4: Responsive Grid Adaptation**
    - **Validates: Requirements 8.1, 8.2, 8.3, 8.4**
    - Générer des largeurs d'écran aléatoires
    - Vérifier que le nombre de colonnes est correct
    - Minimum 100 itérations

  - [ ]* 9.5 Écrire le test de propriété pour Image Association Shuffling
    - **Property 5: Image Association Shuffling**
    - **Validates: Requirements 3.2**
    - Générer des paires d'images aléatoires
    - Vérifier que l'ordre de la colonne B est différent
    - Minimum 100 itérations

  - [ ]* 9.6 Écrire le test de propriété pour Accessibility Attributes Presence
    - **Property 6: Accessibility Attributes Presence**
    - **Validates: Requirements 6.1, 6.2, 6.3, 6.4**
    - Générer des images aléatoires
    - Vérifier la présence d'attributs alt et ARIA
    - Minimum 100 itérations

  - [ ]* 9.7 Écrire le test de propriété pour Security URL Validation
    - **Property 7: Security URL Validation**
    - **Validates: Requirements 10.1, 10.2**
    - Générer des URLs avec protocoles variés
    - Vérifier que seuls http/https sont acceptés
    - Minimum 100 itérations

  - [ ]* 9.8 Écrire le test de propriété pour Preloading Completion Before Display
    - **Property 8: Preloading Completion Before Display**
    - **Validates: Requirements 5.1, 5.5**
    - Générer des questions avec nombre variable d'images
    - Vérifier que toutes les images sont chargées avant affichage
    - Minimum 100 itérations

- [ ] 10. Tests d'intégration
  - [ ]* 10.1 Écrire les tests d'intégration pour le flux complet
    - Tester le chargement d'un quiz avec images
    - Tester la navigation entre questions avec préchargement
    - Tester la soumission de réponses avec images sélectionnées
    - _Requirements: 1.1, 2.1, 3.1, 5.1_

- [ ] 11. Checkpoint final - Vérifier l'intégration complète
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Les tâches marquées avec `*` sont optionnelles et peuvent être sautées pour un MVP plus rapide
- Chaque tâche référence les requirements spécifiques pour la traçabilité
- Les checkpoints permettent de valider l'avancement de manière incrémentale
- Les tests property-based valident les propriétés de correction universelles
- Les tests unitaires valident des exemples spécifiques et des cas limites
- La bibliothèque fast-check est recommandée pour les tests property-based en JavaScript
