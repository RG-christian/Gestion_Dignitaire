# 🎨 Pages Candidature - Documentation

**Date** : 17 juin 2026  
**Design** : Moderne, professionnel, couleurs gabonaises  
**Stack** : Nuxt 3 + TailwindCSS

---

## 📄 Pages créées

### 1️⃣ Page d'Accueil (`/accueil`)
**Fichier** : `pages/accueil.vue`

#### Sections principales :
- ✅ **Navbar flottante** avec logo et navigation
- ✅ **Hero moderne** avec gradient et floating cards
- ✅ **Statistiques** (500+ dignitaires, 98% satisfaction, support 24/7)
- ✅ **Fonctionnalités** (6 cartes avec icônes et gradients)
- ✅ **Processus en 4 étapes** avec timeline visuelle
- ✅ **Footer complet** avec liens et contact

#### Design :
- Navbar fixed avec backdrop-blur et shadow-xl
- Background avec blobs colorés (gabon-green, gabon-blue, gabon-yellow)
- Cards avec hover effects et transitions
- Floating cards animées (animate-float)
- Gradients gabonais partout
- Mobile responsive avec menu hamburger

#### Navigation :
- Lien vers `/candidature` (inscription)
- Lien vers `/candidature/login` (connexion)
- Ancres internes (#accueil, #fonctionnalites, #processus)

---

### 2️⃣ Formulaire de Candidature (`/candidature`)
**Fichier** : `pages/candidature/index.vue`

#### Étapes du formulaire :
1. **Informations personnelles** (Step 1)
   - Nom, prénom, date de naissance, genre
   - Email, téléphone, état civil, adresse
   - Mot de passe + confirmation
   
2. **Upload de documents** (Step 2)
   - Zone drag & drop moderne
   - Upload multiple de fichiers
   - Sélection du type de document par fichier
   - Liste des fichiers avec taille et type
   
3. **Confirmation** (Step 3)
   - Résumé des informations
   - Liste des documents uploadés
   - Checkbox acceptation des conditions
   - Bouton de soumission finale

#### Fonctionnalités :
- ✅ Progress bar (étapes 1/3, 2/3, 3/3)
- ✅ Validation des champs à chaque étape
- ✅ Gestion des fichiers (add, remove, formatSize)
- ✅ Upload API avec FormData
- ✅ Redirection vers `/candidat/dashboard` après soumission
- ✅ Messages d'erreur avec SweetAlert2
- ✅ Loader pendant la soumission

#### Design :
- Navbar simplifiée avec lien vers login
- Card principale avec border et shadow-2xl
- Inputs avec focus:ring-gabon-green-500
- Zone drop avec border-dashed et hover effects
- Boutons avec gradients gabonais

---

### 3️⃣ Page de Connexion (`/candidature/login`)
**Fichier** : `pages/candidature/login.vue`

#### Fonctionnalités :
- ✅ Formulaire email + mot de passe
- ✅ Checkbox "Se souvenir de moi"
- ✅ Lien "Mot de passe oublié ?"
- ✅ Lien vers inscription
- ✅ Authentification API avec token
- ✅ Stockage du token dans localStorage
- ✅ Redirection vers `/candidat/dashboard`
- ✅ Gestion des erreurs (403, 401, etc.)

#### Design :
- Page centrée avec background gradient
- Card avec header coloré (gradient gabon-green)
- Inputs avec icônes (email, lock)
- Bouton gradient avec loader
- Divider "ou" entre login et inscription
- Lien retour vers accueil

---

### 4️⃣ Dashboard Candidat (`/candidat/dashboard`)
**Fichier** : `pages/candidat/dashboard.vue`

#### Sections principales :
- ✅ **Navbar** avec avatar et bouton déconnexion
- ✅ **Header** avec avatar, infos et badge de statut
- ✅ **Message selon statut** (en_attente / valide / refuse)
- ✅ **Informations personnelles** (nom, date naissance, genre, etc.)
- ✅ **Liste des documents** avec icônes et taille
- ✅ **Timeline chronologique** (3 étapes avec checkmarks)
- ✅ **Card support** avec gradient

#### Statuts possibles :
1. **en_attente** ⏳
   - Badge jaune
   - Message : "Candidature en cours d'examen"
   - Timeline : 1 étape validée
   
2. **valide** ✅
   - Badge vert
   - Message : "Félicitations ! Candidature acceptée"
   - Timeline : 3 étapes validées
   
3. **refuse** ❌
   - Badge rouge
   - Message avec motif de refus
   - Suggestion de soumettre une nouvelle candidature

#### Fonctionnalités :
- ✅ Chargement des données API (`/candidats/me`)
- ✅ Affichage des documents uploadés
- ✅ Déconnexion avec confirmation SweetAlert2
- ✅ Protection de la route (token requis)
- ✅ Redirection si non authentifié

#### Design :
- Avatar avec initiales et badge de statut
- Cards avec shadow-xl et border
- Messages colorés selon le statut (yellow, green, red)
- Timeline visuelle avec checkmarks
- Grid responsive (lg:grid-cols-3)
- Sidebar avec timeline et support

---

## 🎨 Design System

### Couleurs Gabonaises (TailwindCSS)
```javascript
colors: {
  'gabon-green': {
    50: '#f0fdf4',
    100: '#dcfce7',
    // ...
    600: '#16a34a',  // Couleur principale
    700: '#15803d',
  },
  'gabon-yellow': {
    600: '#ca8a04',
    700: '#a16207',
  },
  'gabon-blue': {
    600: '#2563eb',
    700: '#1d4ed8',
  }
}
```

### Composants Réutilisables

#### Boutons
```vue
<!-- Bouton principal -->
<button class="px-8 py-3 bg-gradient-to-r from-gabon-green-600 to-gabon-green-700 hover:from-gabon-green-700 hover:to-gabon-green-800 text-white font-bold rounded-xl shadow-lg shadow-gabon-green-600/30 hover:shadow-xl transition-all duration-300">
  Texte
</button>

<!-- Bouton secondaire -->
<button class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:border-gabon-green-600 transition-all duration-300">
  Texte
</button>
```

#### Cards
```vue
<!-- Card standard -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
  <!-- Contenu -->
</div>

<!-- Card avec hover -->
<div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl border border-gray-200 hover:border-gabon-green-600 transition-all duration-300 cursor-pointer">
  <!-- Contenu -->
</div>
```

#### Inputs
```vue
<input class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-gabon-green-500 focus:border-transparent transition-all" />
```

#### Navbar Flottante
```vue
<nav class="fixed top-4 left-4 right-4 z-50">
  <div class="max-w-7xl mx-auto">
    <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-gray-200/50 px-6 py-4">
      <!-- Contenu -->
    </div>
  </div>
</nav>
```

### Animations CSS
```css
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-20px); }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}
```

---

## 🔗 Navigation & Routes

```
/accueil → Page d'accueil publique
/candidature → Formulaire d'inscription (3 étapes)
/candidature/login → Connexion candidat
/candidat/dashboard → Dashboard candidat (protégé)
```

### Protection des routes
Les routes `/candidat/*` nécessitent un token valide stocké dans `localStorage.candidat_token`

---

## 📡 Intégration API

### Endpoints utilisés

#### Inscription
```javascript
POST /api/candidats/register
Body: { nom, prenom, email, password, ... }
Response: { success, token, candidat }
```

#### Connexion
```javascript
POST /api/candidats/login
Body: { email, password }
Response: { success, token, candidat }
```

#### Profil candidat
```javascript
GET /api/candidats/me
Headers: { Authorization: Bearer {token} }
Response: { success, candidat }
```

#### Upload document
```javascript
POST /api/candidats/me/documents
Headers: { Authorization: Bearer {token}, Content-Type: multipart/form-data }
Body: FormData { fichier, type_document }
Response: { success, document }
```

#### Déconnexion
```javascript
POST /api/candidats/logout
Headers: { Authorization: Bearer {token} }
Response: { success }
```

---

## ✅ Checklist UI/UX (Respectée)

### Visuels
- [x] Pas d'emojis comme icônes (SVG uniquement)
- [x] Icônes cohérentes (Heroicons)
- [x] Hover states sans layout shift
- [x] Transitions smooth (150-300ms)

### Interaction
- [x] `cursor-pointer` sur tous les éléments cliquables
- [x] Feedback visuel sur hover
- [x] Focus states visibles (ring-2)

### Layout
- [x] Navbar flottante avec spacing (top-4 left-4 right-4)
- [x] Padding pour éviter le chevauchement
- [x] Responsive (mobile, tablet, desktop)

### Accessibilité
- [x] Labels pour tous les inputs
- [x] Attributs alt pour les images (si ajoutées)
- [x] Focus visible pour navigation clavier

---

## 🚀 Pour tester

### 1. Démarrer le backend
```bash
cd backend
php artisan serve
```

### 2. Démarrer le frontend
```bash
cd frontend
npm run dev
```

### 3. Tester le flux complet
1. Aller sur `http://localhost:3000/accueil`
2. Cliquer sur "Devenir Dignitaire"
3. Remplir le formulaire (3 étapes)
4. Se connecter avec les identifiants créés
5. Voir le dashboard avec le statut "En attente"

---

## 📝 Prochaines améliorations

### Court terme
- [ ] Page admin de validation des candidatures
- [ ] Notifications email (validation/refus)
- [ ] Édition du profil candidat
- [ ] Upload photo de profil

### Moyen terme
- [ ] Modal de prévisualisation des documents
- [ ] Drag & drop amélioré avec progress bars
- [ ] Recherche et filtres dans le dashboard admin
- [ ] Export PDF de la fiche candidat

### Long terme
- [ ] Notifications temps réel (WebSocket)
- [ ] Chat avec le support
- [ ] Système de notes/commentaires par admin
- [ ] Historique des modifications

---

**Statut** : ✅ 3 pages complètes et fonctionnelles  
**Design** : ✅ Professionnel, moderne, couleurs gabonaises  
**Responsive** : ✅ Mobile, tablet, desktop  
**Intégration API** : ✅ Complète avec gestion d'erreurs

