# 🚀 Optimisation Performance - Page Villes

## 🐌 Problèmes Identifiés

### 1. Chargement Initial Lent
- ✅ **583+ villes** chargées d'un coup
- ✅ **190 provinces** chargées d'un coup  
- ✅ **40 pays** chargés d'un coup
- ❌ **Résultat** : 3-5 secondes de chargement

### 2. Recherche Lente
- ❌ Requête à **chaque frappe** de touche
- ❌ Pas de **debounce** (attente)
- ❌ Filtrage **côté client** (lent avec beaucoup de données)

### 3. Pagination Côté Client
- ❌ Toutes les villes en mémoire
- ❌ Filtrage en JavaScript (lent)
- ❌ Pas de limite de données

---

## ✅ Solutions Implémentées

### 1. Backend - Pagination Côté Serveur

**Fichier** : `VilleController.php`

```php
public function index(Request $request): JsonResponse
{
    $perPage = $request->get('per_page', 20); // 20 villes par page
    $page = $request->get('page', 1);
    
    $query = DB::table('ville as v')
        ->select([/* ... */])
        ->leftJoin('pays as p', 'v.pays_id', '=', 'p.id')
        ->leftJoin('region as r', 'v.region_id', '=', 'r.id');

    // Filtres
    if ($request->has('search') && $request->search) {
        $query->where(/* recherche */);
    }

    // Compter le total
    $total = $query->count();
    
    // Pagination
    $villes = $query->orderBy('v.nom')
        ->skip(($page - 1) * $perPage)
        ->take($perPage)
        ->get();

    return response()->json([
        'data' => $villes,
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $page,
        'last_page' => ceil($total / $perPage)
    ]);
}
```

**Avantages** :
- ✅ Charge seulement 20 villes à la fois
- ✅ Recherche en SQL (rapide)
- ✅ Moins de données transférées

### 2. Frontend - Debounce sur la Recherche

**Fichier** : `composables/useDebounce.js`

```javascript
export const useDebounce = () => {
  let timeout = null

  const debounce = (func, delay = 500) => {
    return (...args) => {
      if (timeout) clearTimeout(timeout)
      timeout = setTimeout(() => {
        func(...args)
      }, delay)
    }
  }

  return { debounce }
}
```

**Utilisation** :
```javascript
const { debounce } = useDebounce()
const loadVillesDebounced = debounce(loadVilles, 500)

// Dans le template
<input @input="handleSearch" />

// Dans le script
function handleSearch() {
  currentPage.value = 1
  loadVillesDebounced() // Attend 500ms avant de chercher
}
```

**Avantages** :
- ✅ Attend 500ms avant de lancer la recherche
- ✅ Évite les requêtes inutiles
- ✅ Meilleure expérience utilisateur

### 3. Lazy Loading des Provinces

```javascript
// Charger les provinces uniquement quand nécessaire
async function loadProvinces() {
  if (provinces.value.length > 0) return // Déjà chargées
  
  loadingProvinces.value = true
  try {
    const response = await $fetch(/* ... */)
    provinces.value = response.filter(r => r.type === 'province')
  } finally {
    loadingProvinces.value = false
  }
}

// Appeler uniquement à l'ouverture du modal
function openModal(ville = null) {
  loadProvinces() // Charge les provinces si pas déjà fait
  showModal.value = true
}
```

**Avantages** :
- ✅ Provinces chargées uniquement si nécessaire
- ✅ Pas de chargement au démarrage
- ✅ Mise en cache après le premier chargement

---

## 📊 Résultats Attendus

### Avant Optimisation
| Action | Temps | Données |
|--------|-------|---------|
| Chargement initial | 3-5s | 583 villes + 190 provinces |
| Recherche (par frappe) | 0.5s | Toutes les villes |
| Changement de page | 0.1s | Filtrage client |
| **Total données** | - | **~800 enregistrements** |

### Après Optimisation
| Action | Temps | Données |
|--------|-------|---------|
| Chargement initial | 0.5-1s | 20 villes + 40 pays |
| Recherche (debounce) | 0.3s | 20 villes filtrées |
| Changement de page | 0.3s | 20 nouvelles villes |
| **Total données** | - | **~60 enregistrements** |

### Gain de Performance
- ⚡ **Chargement initial** : 5x plus rapide
- ⚡ **Recherche** : 2x plus rapide
- ⚡ **Mémoire** : 13x moins de données

---

## 🔧 Modifications à Appliquer

### 1. Backend (Déjà fait ✅)
- ✅ `VilleController.php` : Pagination serveur

### 2. Frontend (À faire)

#### Modifier le template
```vue
<!-- Recherche avec debounce -->
<input
  v-model="filters.search"
  @input="handleSearch"
  type="text"
  placeholder="Rechercher une ville..."
/>

<!-- Filtre pays -->
<select
  v-model="filters.pays_id"
  @change="handlePaysFilter"
>
  <option value="">Tous les pays</option>
  <option v-for="p in paysList" :key="p.id" :value="p.id">
    {{ p.nom }}
  </option>
</select>

<!-- Pagination avec événement -->
<Pagination
  :current-page="currentPage"
  :total-pages="totalPages"
  :start-index="startIndex"
  :end-index="endIndex"
  :total="totalVilles"
  @update:current-page="changePage"
/>
```

#### Modifier le script
```javascript
import { useDebounce } from '~/composables/useDebounce'

const { debounce } = useDebounce()
const loadVillesDebounced = debounce(loadVilles, 500)

// Variables
const totalVilles = ref(0)
const totalPages = ref(1)
const perPage = 20

// Charger avec pagination
async function loadVilles() {
  loading.value = true
  try {
    const response = await $fetch(`${config.public.apiBase}/villes-crud`, {
      params: { 
        search: filters.value.search,
        pays_id: filters.value.pays_id,
        page: currentPage.value,
        per_page: perPage
      },
      headers: { /* ... */ }
    })
    
    villes.value = response.data || []
    totalVilles.value = response.total || 0
    totalPages.value = response.last_page || 1
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}

// Recherche avec debounce
function handleSearch() {
  currentPage.value = 1
  loadVillesDebounced()
}

// Filtre pays
function handlePaysFilter() {
  currentPage.value = 1
  loadVilles()
}

// Changement de page
function changePage(page) {
  currentPage.value = page
  loadVilles()
}

// Lazy loading provinces
async function loadProvinces() {
  if (provinces.value.length > 0) return
  // Charger...
}

// Initialisation
onMounted(async () => {
  await loadPays() // Seulement les pays
  await loadVilles() // Première page
  // Provinces chargées à la demande
})
```

---

## 🎯 Checklist d'Implémentation

### Backend
- [x] Ajouter pagination dans `VilleController.php`
- [x] Retourner `data`, `total`, `per_page`, `current_page`, `last_page`
- [x] Tester avec Postman/Insomnia

### Frontend
- [ ] Créer `composables/useDebounce.js`
- [ ] Modifier `pages/villes/index.vue` :
  - [ ] Importer `useDebounce`
  - [ ] Ajouter `totalVilles`, `totalPages`, `perPage`
  - [ ] Modifier `loadVilles()` pour gérer la pagination
  - [ ] Créer `handleSearch()` avec debounce
  - [ ] Créer `handlePaysFilter()`
  - [ ] Créer `changePage()`
  - [ ] Modifier `loadProvinces()` pour lazy loading
  - [ ] Mettre à jour le template

### Tests
- [ ] Tester le chargement initial (doit être rapide)
- [ ] Tester la recherche (doit attendre 500ms)
- [ ] Tester le filtre pays (doit être instantané)
- [ ] Tester la pagination (doit charger 20 villes)
- [ ] Tester l'ajout de ville (provinces lazy loaded)

---

## 📝 Notes Importantes

### Debounce
- **500ms** est un bon compromis
- Trop court (100ms) : Trop de requêtes
- Trop long (1000ms) : Semble lent

### Pagination
- **20 villes par page** est optimal
- Trop peu (10) : Trop de clics
- Trop (50) : Trop lent

### Lazy Loading
- Charger les provinces uniquement à l'ouverture du modal
- Mettre en cache après le premier chargement
- Pas besoin de recharger à chaque fois

---

## 🚀 Prochaines Optimisations

### Cache Côté Client
```javascript
// Mettre en cache les résultats de recherche
const searchCache = new Map()

async function loadVilles() {
  const cacheKey = `${filters.value.search}-${filters.value.pays_id}-${currentPage.value}`
  
  if (searchCache.has(cacheKey)) {
    villes.value = searchCache.get(cacheKey)
    return
  }
  
  // Charger depuis le serveur
  const response = await $fetch(/* ... */)
  searchCache.set(cacheKey, response.data)
  villes.value = response.data
}
```

### Indexation Base de Données
```sql
-- Ajouter des index pour accélérer les recherches
CREATE INDEX idx_ville_nom ON ville(nom);
CREATE INDEX idx_ville_pays_id ON ville(pays_id);
CREATE INDEX idx_ville_region_id ON ville(region_id);
```

### Compression Gzip
```php
// Dans le middleware Laravel
return response()->json($data)->header('Content-Encoding', 'gzip');
```

---

**Date** : 21 Mai 2026  
**Statut** : Backend ✅ | Frontend ⏳  
**Gain attendu** : 5x plus rapide
