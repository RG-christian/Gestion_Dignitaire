# 🆕 Nouveaux Modèles Laravel - Phase 1

**Date** : 17 juin 2026  
**Objectif** : Modèles Eloquent pour le système de candidatures

---

## 📦 Modèles créés

### 1️⃣ `Candidat.php`

**Type** : `Authenticatable` (peut se connecter)  
**Table** : `candidats`

#### Fonctionnalités principales :
- ✅ Authentification avec Sanctum (API tokens)
- ✅ Notifications Laravel
- ✅ Scopes de filtrage (enAttente, valide, refuse, search, byGenre)
- ✅ Accesseurs : nom_complet, age, status_badge
- ✅ Méthodes métier : valider(), refuser(), peutSeConnecter(), estModifiable()

#### Relations :
- `lieuNaissance()` → BelongsTo Ville
- `villeResidence()` → BelongsTo Ville
- `validePar()` → BelongsTo User (admin)
- `dignitaire()` → BelongsTo Dignitaire (après validation)
- `documents()` → HasMany CandidatDocument

#### Attributs cachés :
- `password`
- `remember_token`

---

### 2️⃣ `CandidatDocument.php`

**Type** : `Model`  
**Table** : `candidat_documents`

#### Fonctionnalités principales :
- ✅ Gestion automatique des fichiers (suppression physique sur delete)
- ✅ Scopes : byType, PDF, images
- ✅ Accesseurs : taille_lisible, url_complete, icone_type
- ✅ Méthodes métier : estImage(), estPDF()

#### Relations :
- `candidat()` → BelongsTo Candidat

#### Types de documents supportés :
- `diplome` 🎓 (bleu)
- `cv` 📄 (gris)
- `lettre` ✉️ (vert)
- `attestation` 📜 (jaune)
- `casier` 🔒 (rouge)
- `medical` ⚕️ (violet)
- `passeport` 🛂 (indigo)
- `autre` 📎 (gris)

#### Événement boot() :
```php
static::deleting(function ($document) {
    // Supprime automatiquement le fichier physique
    Storage::delete($document->chemin_fichier);
});
```

---

### 3️⃣ `Conjoint.php`

**Type** : `Model`  
**Table** : `conjoints`

#### Fonctionnalités principales :
- ✅ Scopes : actifs, militaires, dignitaires, byStatut, byGenre, search
- ✅ Accesseurs : nom_complet, age, duree_mariage, status_badge
- ✅ Méthodes métier : estMarie(), aStatutSpecial(), getStatutSpecial(), terminerUnion()

#### Relations :
- `dignitaire()` → BelongsTo Dignitaire
- `lieuNaissance()` → BelongsTo Ville
- `nationalite()` → BelongsTo Pays

#### Statuts possibles :
- `actif` ✅ (vert) - Toujours marié
- `divorce` 💔 (orange)
- `veuf` 🖤 (gris)
- `separe` 💔 (jaune)

#### Statut spécial (recommandation Marcel) :
- `est_militaire` : boolean
- `est_dignitaire` : boolean
- `grade_militaire` : string (si militaire)
- `fonction_dignitaire` : string (si dignitaire)

---

## 🔗 Modifications des modèles existants

### `Dignitaire.php` - Ajout relations conjoints

```php
public function conjoints(): HasMany
{
    return $this->hasMany(Conjoint::class);
}

public function conjointActif(): HasMany
{
    return $this->hasMany(Conjoint::class)->where('statut', 'actif');
}
```

---

## 📊 Relations complètes

```
Candidat
├── lieuNaissance() → Ville
├── villeResidence() → Ville
├── validePar() → User
├── dignitaire() → Dignitaire (après validation)
└── documents() → CandidatDocument[]

CandidatDocument
└── candidat() → Candidat

Conjoint
├── dignitaire() → Dignitaire
├── lieuNaissance() → Ville
└── nationalite() → Pays

Dignitaire
├── ... (relations existantes)
├── conjoints() → Conjoint[]
└── conjointActif() → Conjoint[] (filtre statut='actif')
```

---

## 🎯 Utilisation dans les contrôleurs

### Exemple : Récupérer un candidat avec ses documents

```php
$candidat = Candidat::with([
    'documents',
    'lieuNaissance',
    'villeResidence'
])->findOrFail($id);
```

### Exemple : Filtrer les candidatures en attente

```php
$candidatsEnAttente = Candidat::enAttente()
    ->with('documents')
    ->latest('date_candidature')
    ->paginate(20);
```

### Exemple : Valider une candidature

```php
// Créer le dignitaire
$dignitaire = Dignitaire::create([...]);

// Marquer le candidat comme validé
$candidat->valider(auth()->id(), $dignitaire->id);
```

### Exemple : Récupérer les conjoints d'un dignitaire

```php
$dignitaire = Dignitaire::with([
    'conjoints.lieuNaissance',
    'conjoints.nationalite'
])->findOrFail($id);

// Seulement les conjoints actifs
$conjoints = $dignitaire->conjointActif;

// Conjoints militaires
$conjointsMilitaires = $dignitaire->conjoints()->militaires()->get();
```

### Exemple : Terminer une union

```php
$conjoint = Conjoint::findOrFail($id);
$conjoint->terminerUnion('divorce', now());
```

---

## 🔐 Authentification des candidats

Les candidats peuvent se connecter avec **Sanctum** :

```php
// Login candidat
$candidat = Candidat::where('email', $request->email)->first();

if ($candidat && Hash::check($request->password, $candidat->password)) {
    $token = $candidat->createToken('candidat-token')->plainTextToken;
    
    return response()->json([
        'token' => $token,
        'candidat' => $candidat,
    ]);
}
```

---

## 🧪 Tests recommandés

### Candidat
- ✅ Création d'un candidat
- ✅ Upload de documents
- ✅ Connexion candidat
- ✅ Validation par admin
- ✅ Refus avec motif
- ✅ Conversion candidat → dignitaire

### CandidatDocument
- ✅ Upload fichier
- ✅ Suppression fichier (physique + BDD)
- ✅ Filtrage par type

### Conjoint
- ✅ Ajout d'un conjoint
- ✅ Modification statut (divorce, veuf)
- ✅ Calcul durée mariage
- ✅ Filtrage conjoints actifs

---

## 📝 Prochaines étapes

Après avoir créé ces modèles :

1. ✅ Créer les contrôleurs API
2. ✅ Définir les routes API
3. ✅ Créer les FormRequests pour validation
4. ✅ Créer les Resources pour transformer les réponses JSON

---

**Statut** : ✅ Modèles créés avec toutes les relations et méthodes métier
