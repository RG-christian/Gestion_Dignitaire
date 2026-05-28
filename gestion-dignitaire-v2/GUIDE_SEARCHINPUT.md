# 🔍 Guide d'Utilisation du Composant SearchInput

## 📖 Introduction

Le composant `SearchInput` est un champ de recherche moderne et réutilisable qui remplace les anciennes barres de recherche dans toutes les pages de l'application.

## 🎨 Apparence

```
┌─────────────────────────────────────────────────────┐
│  🔍  Rechercher...                              ✕   │
└─────────────────────────────────────────────────────┘
     ↑                                            ↑
  Icône loupe                              Bouton clear
  (toujours visible)                    (visible si texte)
```

## 🎯 Fonctionnalités

### 1. Recherche en Temps Réel
- Tapez du texte → La recherche se déclenche automatiquement après 500ms
- Optimisation AJAX : Pas de requête à chaque caractère

### 2. Bouton Clear (✕)
- Apparaît automatiquement quand vous tapez du texte
- Clic sur ✕ → Efface le texte et recharge la liste complète
- Gain de temps : Plus besoin de tout effacer manuellement

### 3. Touche Enter
- Appuyez sur Enter → Déclenche la recherche immédiatement
- Utile pour les recherches rapides

### 4. Focus Ring
- Cliquez dans le champ → Bordure verte gabonaise apparaît
- Indication visuelle claire du champ actif

## 📍 Où le Trouver ?

Le composant est intégré dans **7 barres de recherche** :

### 1. Page Postes
- **Recherche 1** : Rechercher des postes (intitulé, dignitaire, entité, ville)
- **Recherche 2** : Rechercher des entités

### 2. Page Enfants
- Rechercher un enfant (nom, prénom, dignitaire)

### 3. Page Diplômes
- Rechercher par intitulé, établissement, année

### 4. Page Pays
- Rechercher par nom, code ISO, continent

### 5. Page Régions
- Rechercher une région ou province

### 6. Page Villes
- Rechercher une ville

## 🎬 Scénarios d'Utilisation

### Scénario 1 : Recherche Simple
```
1. Ouvrir la page Postes
2. Cliquer dans la barre de recherche
3. Taper "Ministre"
4. Attendre 500ms
5. ✅ Les résultats s'affichent automatiquement
```

### Scénario 2 : Effacer la Recherche
```
1. Taper "Ministre" dans la recherche
2. Voir les résultats filtrés
3. Cliquer sur le bouton ✕ (clear)
4. ✅ Le champ est vidé
5. ✅ La liste complète est rechargée
```

### Scénario 3 : Recherche Rapide avec Enter
```
1. Taper "Gabon"
2. Appuyer sur Enter (sans attendre 500ms)
3. ✅ La recherche se déclenche immédiatement
```

### Scénario 4 : Recherche Combinée avec Filtres
```
1. Page Postes : Taper "Ministre" dans la recherche
2. Sélectionner un dignitaire dans le filtre
3. ✅ Les résultats sont filtrés par les 2 critères
```

## 🎨 Design

### Couleurs
- **Bordure normale** : Gris clair (`border-gray-300`)
- **Focus** : Vert gabonais (`focus:ring-gabon-green-500`)
- **Icône loupe** : Gris moyen (`text-gray-400`)
- **Bouton clear** : Gris → Rouge au survol

### Dimensions
- **Hauteur** : 44px (11 en Tailwind)
- **Padding** : 10px gauche (pour l'icône), 10px droite (pour le bouton)
- **Bordure** : 1px arrondie (`rounded-lg`)

### Animations
- **Focus ring** : Apparition douce (transition)
- **Bouton clear** : Changement de couleur au survol
- **Icônes** : Taille 16px (4 en Tailwind)

## 🔧 Personnalisation (Pour Développeurs)

### Props Disponibles

```vue
<SearchInput
  v-model="filters.search"           // Valeur du champ (requis)
  placeholder="Rechercher..."        // Texte du placeholder
  label="Rechercher un dignitaire"   // Label au-dessus (optionnel)
  :disabled="loading"                // Désactiver le champ
  :show-submit-button="true"         // Bouton submit au lieu de clear
  :show-clear-button="false"         // Masquer le bouton clear
  submit-label="Rechercher"          // Label du bouton submit
  @update:modelValue="debouncedLoad" // Événement de changement
  @submit="handleSubmit"             // Événement de soumission
  @clear="handleClear"               // Événement de clear
/>
```

### Événements

| Événement | Quand | Paramètre |
|-----------|-------|-----------|
| `@update:modelValue` | À chaque changement de texte | `value: string` |
| `@submit` | Enter ou clic bouton submit | `value: string` |
| `@clear` | Clic sur bouton clear | Aucun |

## 📊 Performance

### Optimisation AJAX
- **Sans debounce** : 25 caractères = 25 requêtes
- **Avec debounce** : 25 caractères = 1 requête
- **Réduction** : 96% de requêtes en moins

### Exemple Concret
```
Taper "Ministre de l'Économie" (24 caractères)

Sans SearchInput + Debounce :
M → requête
Mi → requête
Min → requête
... (24 requêtes au total)

Avec SearchInput + Debounce :
M
Mi
Min
...
Ministre de l'Économie → 1 seule requête après 500ms
```

## ✅ Avantages

### Pour l'Utilisateur
1. **Plus rapide** : Bouton clear pour effacer rapidement
2. **Plus clair** : Icônes visuelles (loupe, croix)
3. **Plus fluide** : Animations et transitions douces
4. **Plus cohérent** : Même design sur toutes les pages

### Pour le Développeur
1. **Moins de code** : Composant réutilisable
2. **Plus maintenable** : Un seul fichier à modifier
3. **Plus propre** : Pas de duplication de code
4. **Plus flexible** : Props configurables

## 🐛 Dépannage

### Problème : La recherche ne fonctionne pas
**Solution** : Vérifiez que vous utilisez `@update:modelValue` et non `@input`

```vue
<!-- ❌ Incorrect -->
<SearchInput v-model="filters.search" @input="debouncedLoad" />

<!-- ✅ Correct -->
<SearchInput v-model="filters.search" @update:modelValue="debouncedLoad" />
```

### Problème : Le bouton clear n'apparaît pas
**Solution** : Vérifiez que `showClearButton` n'est pas à `false`

```vue
<!-- ❌ Bouton clear masqué -->
<SearchInput v-model="filters.search" :show-clear-button="false" />

<!-- ✅ Bouton clear visible (défaut) -->
<SearchInput v-model="filters.search" />
```

### Problème : Le debounce ne fonctionne pas
**Solution** : Vérifiez que la fonction est bien debouncée

```vue
<script setup>
const { debounce } = useDebounce()

// ✅ Correct : Fonction debouncée
const debouncedLoad = debounce(loadData, 500)

// ❌ Incorrect : Fonction normale
const loadData = () => { /* ... */ }
</script>
```

## 📚 Ressources

- **Documentation complète** : `INTEGRATION_SEARCHINPUT.md`
- **Résumé** : `RESUME_INTEGRATION_SEARCHINPUT.md`
- **Optimisation AJAX** : `OPTIMISATION_RECHERCHE.md`
- **Composant** : `frontend/components/SearchInput.vue`
- **Composable Debounce** : `frontend/composables/useDebounce.ts`

## 🎉 Conclusion

Le composant `SearchInput` améliore l'expérience utilisateur avec un design moderne, des fonctionnalités pratiques (bouton clear, Enter), et une optimisation des performances (debounce). Il est maintenant intégré dans 7 barres de recherche à travers l'application.
