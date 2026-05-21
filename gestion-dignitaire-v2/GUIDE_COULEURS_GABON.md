# Guide des Couleurs - Gestion des Dignitaires 🇬🇦

## Palette Officielle - République Gabonaise

Ce guide définit les couleurs à utiliser dans toute l'application, basées sur les couleurs du drapeau national gabonais.

---

## 🎨 Couleurs Principales

### 🟢 VERT (Couleur Primaire)
**Symbolise** : Les forêts et la nature du Gabon

**Utilisation** :
- Boutons d'action principaux (Ajouter, Enregistrer, Valider)
- En-têtes de sections importantes
- Indicateurs de succès
- Navigation active

**Classes Tailwind** :
```html
<!-- Backgrounds -->
bg-gabon-green-50   <!-- Très clair -->
bg-gabon-green-600  <!-- Principal -->
bg-gabon-green-700  <!-- Hover -->

<!-- Text -->
text-gabon-green-600
text-gabon-green-700

<!-- Borders -->
border-gabon-green-600

<!-- Gradients -->
from-gabon-green-600 to-gabon-green-700
```

**Exemples** :
- ✅ Bouton "Ajouter un diplôme"
- ✅ En-tête de modal d'ajout
- ✅ Icônes principales
- ✅ Focus states des inputs

---

### 🟡 JAUNE/OR (Couleur Secondaire)
**Symbolise** : Le soleil équatorial et les richesses du Gabon

**Utilisation** :
- Badges et étiquettes importantes
- Highlights et mises en évidence
- Indicateurs d'avertissement
- Accents visuels

**Classes Tailwind** :
```html
<!-- Backgrounds -->
bg-gabon-yellow-50   <!-- Très clair -->
bg-gabon-yellow-500  <!-- Principal -->
bg-gabon-yellow-600  <!-- Hover -->

<!-- Text -->
text-gabon-yellow-600
text-gabon-yellow-700

<!-- Borders -->
border-gabon-yellow-500
```

**Exemples** :
- 🏷️ Badge "Année" sur les cards
- ⚠️ Messages d'avertissement
- ⭐ Éléments à mettre en valeur
- 📌 Icônes secondaires

---

### 🔵 BLEU (Couleur Tertiaire)
**Symbolise** : L'océan Atlantique

**Utilisation** :
- Boutons d'information et de consultation
- Modals de détails
- Liens et navigation secondaire
- Éléments informatifs

**Classes Tailwind** :
```html
<!-- Backgrounds -->
bg-gabon-blue-50    <!-- Très clair -->
bg-gabon-blue-600   <!-- Principal -->
bg-gabon-blue-700   <!-- Hover -->

<!-- Text -->
text-gabon-blue-600
text-gabon-blue-700

<!-- Borders -->
border-gabon-blue-600
```

**Exemples** :
- 👁️ Bouton "Détail"
- 📄 En-tête de modal de détail
- 🔗 Liens de navigation
- ℹ️ Icônes d'information

---

## 🎯 Couleurs Génériques

### 🔴 ROUGE (Actions Destructives)
**Utilisation** : Suppression, erreurs, alertes critiques

```html
bg-red-50
bg-red-600
text-red-600
border-red-600
```

**Exemples** :
- 🗑️ Bouton "Supprimer"
- ❌ Messages d'erreur
- ⚠️ Alertes critiques

---

### ⚪ GRIS (Neutre)
**Utilisation** : Textes, bordures, backgrounds neutres, états désactivés

```html
bg-gray-50    <!-- Backgrounds légers -->
bg-gray-100   <!-- Backgrounds cards -->
bg-gray-200   <!-- Bordures -->
text-gray-500 <!-- Textes secondaires -->
text-gray-700 <!-- Textes principaux -->
text-gray-800 <!-- Titres -->
```

---

### 🔵 SKY (Informations)
**Utilisation** : Boutons d'information secondaires, états informatifs

```html
bg-sky-50
bg-sky-600
text-sky-700
```

**Exemples** :
- 👁️ Bouton "Voir" alternatif
- ℹ️ Messages informatifs

---

## 📋 Règles d'Application

### 1. Hiérarchie des Couleurs

**Ordre de priorité** :
1. **Vert** → Actions principales et positives
2. **Bleu** → Consultation et information
3. **Jaune** → Mise en évidence et avertissements
4. **Rouge** → Actions destructives uniquement
5. **Gris** → Éléments neutres

### 2. Par Type de Page

#### Pages de Gestion (Liste)
- **En-tête** : Icône verte
- **Bouton principal** : Gradient vert
- **Cards** : En-tête avec gradient vert
- **Badges** : Jaune pour les informations clés
- **Icônes de contenu** : Bleu et jaune en alternance

#### Modals d'Ajout/Modification
- **En-tête** : Gradient vert
- **Bouton de validation** : Gradient vert
- **Focus des inputs** : Ring vert

#### Modals de Détail
- **En-tête** : Gradient bleu
- **Sections importantes** : Background vert clair avec bordure verte

### 3. États Interactifs

```html
<!-- Bouton Vert Principal -->
<button class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 
               hover:from-gabon-green-700 hover:to-gabon-green-800">

<!-- Bouton Bleu Information -->
<button class="bg-gabon-blue-50 hover:bg-gabon-blue-100 text-gabon-blue-700">

<!-- Bouton Rouge Danger -->
<button class="bg-red-50 hover:bg-red-100 text-red-700">

<!-- Input avec focus vert -->
<input class="focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent">
```

---

## 🎨 Exemples de Combinaisons

### Card de Diplôme
```html
<!-- En-tête -->
<div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700">
  <h3 class="text-white">Titre</h3>
  <span class="bg-gabon-yellow-500 text-gray-900">Badge</span>
</div>

<!-- Contenu -->
<div class="p-4">
  <svg class="text-gabon-blue-600"><!-- Icône --></svg>
  <svg class="text-gabon-yellow-600"><!-- Icône --></svg>
</div>
```

### Formulaire
```html
<div class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700">
  <h4 class="text-white">Titre du formulaire</h4>
</div>

<input class="focus:ring-2 focus:ring-gabon-green-500">
<button class="bg-gradient-to-r from-gabon-green-600 to-gabon-green-700">
  Enregistrer
</button>
```

---

## ✅ Checklist de Mise à Jour

Lors de la création ou modification d'une page :

- [ ] Icône principale en vert (`text-gabon-green-600`)
- [ ] Bouton principal avec gradient vert
- [ ] Cards avec en-tête vert
- [ ] Badges importants en jaune
- [ ] Boutons "Détail" en bleu clair
- [ ] Boutons "Supprimer" en rouge
- [ ] Focus des inputs en vert
- [ ] Loader en vert
- [ ] Hover states cohérents

---

## 🚀 Pages à Mettre à Jour

- [x] Diplômes
- [ ] Décorations
- [ ] Nominations
- [ ] Postes
- [ ] Expériences
- [ ] Langues
- [ ] Enfants
- [ ] Dashboard
- [ ] Dignitaires (liste)
- [ ] Dignitaires (détails)

---

**Note** : Ces couleurs représentent l'identité nationale gabonaise et doivent être utilisées de manière cohérente dans toute l'application pour renforcer l'identité visuelle du projet gouvernemental.
