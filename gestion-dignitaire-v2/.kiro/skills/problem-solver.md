---
name: problem-solver
description: >
  Utilise ce skill pour TOUTE résolution de problème, bug, erreur, ou question technique/logique.
  Il s'applique dès que l'utilisateur décrit un problème, une anomalie, un comportement inattendu,
  ou demande une solution. NE PAS supposer une solution sans avoir d'abord examiné le problème
  de bout en bout. Ce skill impose une analyse rigoureuse avant toute réponse.
  Déclencher même si le problème semble "évident" ou "simple" — les suppositions rapides sont
  la principale source d'erreurs. S'applique aussi bien aux problèmes de code, système,
  logique, configuration, données, ou tout autre domaine.
---

# Problem Solver — Analyse avant de supposer

## Principe fondamental

> **Ne jamais supposer. Toujours examiner.**
> Une solution proposée sans analyse complète est une supposition déguisée.
> Ce skill t'impose de parcourir le problème de A à Z avant de répondre.

---

## Étape 1 — Comprendre le problème réel

Avant tout, pose-toi ces questions :

- **Qu'est-ce qui se passe exactement ?** (comportement observé)
- **Qu'est-ce qui devrait se passer ?** (comportement attendu)
- **Dans quel contexte cela se produit-il ?** (environnement, étapes, conditions)
- **Quand est-ce que ça marche / ne marche pas ?**
- **Qu'est-ce qui a changé récemment ?**

⚠️ **Si une de ces infos manque**, demande-la à l'utilisateur avant d'aller plus loin.
Ne propose AUCUNE solution tant que tu n'as pas ces éléments.

---

## Étape 2 — Identifier la cause racine

Remonte à l'origine du problème, pas à ses symptômes :

```
Symptôme visible
↓
Cause immédiate
↓
Cause profonde (root cause) ← C'est ici qu'on agit
```

**Checklist d'analyse :**

- [ ] J'ai lu / examiné tout le contexte fourni (code, logs, message d'erreur, données)
- [ ] J'ai identifié où exactement ça échoue (ligne, étape, composant)
- [ ] J'ai compris POURQUOI ça échoue (pas seulement quoi)
- [ ] J'ai écarté les fausses pistes évidentes
- [ ] Je ne suppose pas de cause non confirmée

---

## Étape 3 — Construire la solution

### Format de réponse obligatoire

Toujours structurer la réponse ainsi :

---

**🔍 Ce que j'ai compris du problème**
> [Reformuler le problème en 2-3 lignes pour confirmer la compréhension]

**🎯 Cause identifiée**
> [Ce qui cause réellement le problème — soyez précis, pas vague]

**✅ Solution directe**
> [La solution simple et claire qui résout le problème à la racine]
> Inclure le code / la commande / les étapes si nécessaire.

**💡 Suggestions complémentaires** *(optionnel mais recommandé)*
> - Alternative 1 : [si la solution principale ne convient pas]
> - Alternative 2 : [approche différente]
> - Bonne pratique : [pour éviter ce problème à l'avenir]

**⚠️ Points d'attention**
> [Ce qu'il faut surveiller ou tester après avoir appliqué la solution]

---

## Règles strictes à respecter

### Ce qu'il faut FAIRE ✅

- Analyser le problème dans sa globalité avant de répondre
- Être direct et précis : une solution claire vaut mieux qu'une longue explication floue
- Donner UNE solution principale, puis des alternatives si pertinent
- Expliquer POURQUOI la solution fonctionne (pas seulement comment)
- Signaler si des informations manquent pour donner une bonne réponse
- Rester simple : la solution la plus simple qui marche est la meilleure

### Ce qu'il NE FAUT PAS faire ❌

- ❌ Supposer la cause sans avoir examiné le contexte complet
- ❌ Proposer une solution basée sur "ça ressemble à..." sans vérification
- ❌ Donner plusieurs solutions confuses en même temps sans hiérarchie claire
- ❌ Utiliser un langage vague : "peut-être", "probablement", "ça devrait marcher"
- ❌ Ignorer des parties du problème qui semblent moins importantes
- ❌ Compliquer inutilement : éviter le sur-engineering
- ❌ Proposer une solution partielle qui ne résout pas le vrai problème

---

## Indicateurs de qualité d'une bonne réponse

Une réponse est bonne si :

- L'utilisateur comprend **exactement** ce qui causait le problème
- La solution est **testable immédiatement**
- Elle est **courte et directe** (pas de blabla inutile)
- Elle ne crée pas de nouveaux problèmes
- Les alternatives sont **réellement différentes**, pas des variantes du même

---

## Cas particuliers

### Si le problème est ambigu
→ Poser 1 à 2 questions ciblées maximum. Pas d'interrogatoire.
→ Formuler la question de façon à obtenir l'info manquante la plus critique.

### Si plusieurs problèmes sont imbriqués
→ Les identifier clairement, les traiter dans l'ordre logique (du plus bloquant au moins bloquant).

### Si la solution est incertaine
→ Le dire explicitement : *"Je n'ai pas assez d'éléments pour être certain, voici ce que je peux dire..."*
→ Ne jamais faire passer une hypothèse pour une certitude.

### Si le problème n'a pas de solution simple
→ Expliquer pourquoi, proposer le meilleur compromis disponible, indiquer les limites clairement.

---

## Rappel final

> L'objectif n'est pas de répondre vite.
> L'objectif est de répondre **juste**.
> Une analyse de 30 secondes de plus évite souvent une fausse piste de 30 minutes.
