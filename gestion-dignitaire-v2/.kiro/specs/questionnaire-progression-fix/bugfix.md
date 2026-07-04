# Bugfix Requirements Document

## Introduction

Le système de questionnaires dans le module "Formalités" ne sauvegardait pas les réponses des utilisateurs. Lorsqu'un utilisateur complétait un questionnaire et cliquait sur "Terminer", la progression restait bloquée à 0% et les modules suivants ne se débloquaient pas, empêchant la progression dans le parcours d'intégration.

Ce bug affectait tous les utilisateurs tentant de compléter les questionnaires du module "Formalités", rendant impossible l'avancement dans le système d'intégration.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN un utilisateur complète un questionnaire et clique sur "Terminer" THEN le système affiche un message de succès mais n'envoie aucune requête au backend

1.2 WHEN un utilisateur termine un questionnaire THEN la progression du module reste à 0% au lieu d'être mise à jour

1.3 WHEN un utilisateur complète tous les questionnaires d'un module THEN les modules suivants restent verrouillés au lieu de se débloquer

1.4 WHEN un utilisateur retourne sur le dashboard après avoir complété un questionnaire THEN aucune réponse n'est sauvegardée dans la base de données

### Expected Behavior (Correct)

2.1 WHEN un utilisateur complète un questionnaire et clique sur "Terminer" THEN le système SHALL envoyer une requête POST à `/api/questionnaires/reponses` avec les réponses de l'utilisateur

2.2 WHEN un utilisateur termine un questionnaire THEN le système SHALL mettre à jour la progression du module (20% par questionnaire complété sur 5 questionnaires)

2.3 WHEN un utilisateur complète tous les questionnaires d'un module (100%) THEN le système SHALL débloquer automatiquement le module suivant dans la séquence

2.4 WHEN un utilisateur retourne sur le dashboard après avoir complété un questionnaire THEN le système SHALL afficher la progression mise à jour et les réponses SHALL être persistées dans la base de données

2.5 WHEN l'envoi des réponses échoue THEN le système SHALL afficher un message d'erreur explicite et permettre à l'utilisateur de réessayer

### Unchanged Behavior (Regression Prevention)

3.1 WHEN un utilisateur navigue entre les questions d'un questionnaire THEN le système SHALL CONTINUE TO stocker temporairement les réponses dans l'état local du composant

3.2 WHEN un utilisateur quitte un questionnaire sans le terminer THEN le système SHALL CONTINUE TO afficher une confirmation et ne pas sauvegarder les réponses partielles

3.3 WHEN un utilisateur visualise un questionnaire THEN le système SHALL CONTINUE TO afficher correctement toutes les questions avec leurs types de champs (texte, radio, checkbox, etc.)

3.4 WHEN un utilisateur répond à une question obligatoire THEN le système SHALL CONTINUE TO valider que la réponse n'est pas vide avant la soumission

3.5 WHEN le backend reçoit des réponses valides THEN le système SHALL CONTINUE TO calculer correctement la progression basée sur le nombre de sous-modules complétés

3.6 WHEN un module atteint 100% de progression THEN le système SHALL CONTINUE TO utiliser le système de déblocage séquentiel existant pour activer le module suivant
