# Optimisation des Recherches AJAX

## Problème Initial

Avant l'optimisation, chaque frappe de touche dans les champs de recherche déclenchait immédiatement une requête HTTP vers le serveur :

```vue
<!-- ❌ AVANT : Pas optimisé -->
<input
  v-model="filters.search"
  @input="loadPostes"  <!-- Appel direct à chaque frappe -->
  type="text"
  placeholder="Rechercher..."
>
```

**Conséquences** :
- Si l'utilisateur tape "Ministre" (8 caractères), cela génère **8 requêtes HTTP**
- Surcharge du serveur avec des requêtes inutiles
- Ralentissement de l'interface
- Gaspillage de bande passante

## Solution : Debounce

Le **debounce** retarde l'exécution de la fonction jusqu'à ce que l'utilisateur arrête de taper pendant un certain délai (500ms par défaut).

### Implémentation

#### 1. Composable `useDebounce.ts`

```typescript
export function useDebounce() {
  const debounce = <T extends (...args: any[]) => any>(
    func: T,
    delay: number = 500
  ): ((...args: Parameters<T>) => void) => {
    let timeoutId: ReturnType<typeof setTimeout> | null = null

    return (...args: Parameters<T>) => {
      if (timeoutId) {
        clearTimeout(timeoutId)
      }

      timeoutId = setTimeout(() => {
        func(...args)
      }, delay)
    }
  }

  return { debounce }
}
```

#### 2. Utilisation dans les pages

```vue
<script setup>
const { debounce } = useDebounce()

// Fonction de chargement normale
async function loadPostes() {
  // ... requête API
}

// Version debouncée (500ms de délai)
const debouncedLoadPostes = debounce(loadPostes, 500)
</script>

<template>
  <!-- ✅ APRÈS : Optimisé avec debounce -->
  <input
    v-model="filters.search"
    @input="debouncedLoadPostes"  <!-- Appel debounced -->
    type="text"
    placeholder="Rechercher..."
  >
</template>
```

## Résultats

### Avant (sans debounce)
- Utilisateur tape "Ministre" → **8 requêtes HTTP**
- Temps total : ~800ms (8 × 100ms)

### Après (avec debounce 500ms)
- Utilisateur tape "Ministre" → **1 seule requête HTTP** (500ms après la dernière frappe)
- Temps total : ~600ms (500ms délai + 100ms requête)

## Bénéfices

✅ **Réduction de 87.5% des requêtes** (8 → 1)  
✅ **Moins de charge serveur**  
✅ **Meilleure expérience utilisateur** (pas de lag)  
✅ **Économie de bande passante**  
✅ **Réduction des coûts d'infrastructure**

## Pages Optimisées

- ✅ **Postes** : Recherche de postes et entités
- ✅ **Enfants** : Recherche d'enfants
- ✅ **Diplômes** : Recherche de diplômes
- ✅ **Pays** : Recherche de pays
- ✅ **Régions** : Recherche de régions et provinces
- ✅ **Villes** : Recherche de villes (refactorisé avec le composable)

**Toutes les pages de recherche sont maintenant optimisées !**

## Configuration

Le délai par défaut est de **500ms** (0.5 seconde). Vous pouvez l'ajuster :

```typescript
// Délai plus court (300ms) pour une réponse plus rapide
const debouncedLoad = debounce(loadData, 300)

// Délai plus long (1000ms) pour réduire encore plus les requêtes
const debouncedLoad = debounce(loadData, 1000)
```

## Recommandations

- **500ms** : Bon équilibre pour la plupart des cas
- **300ms** : Pour les recherches très rapides
- **1000ms** : Pour les recherches lourdes (grandes bases de données)

## Autres Optimisations Possibles

1. **Cache côté client** : Mémoriser les résultats de recherche
2. **Pagination côté serveur** : Ne charger que les résultats visibles
3. **Lazy loading** : Charger les données au scroll
4. **Indexation** : Ajouter des index sur les colonnes de recherche en BDD
5. **Recherche full-text** : Utiliser MySQL FULLTEXT ou Elasticsearch
