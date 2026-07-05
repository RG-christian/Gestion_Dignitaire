# Amélioration du Formulaire de Candidature

## 📋 Résumé des améliorations apportées

### ✅ 1. Sélection du Pays de Naissance AVANT la Ville

**Problème initial** : Toutes les villes s'affichaient dans le champ "Lieu de naissance" sans possibilité de filtrer par pays.

**Solution implémentée** :
- Ajout d'un champ `pays_naissance_id` (obligatoire) avant le champ ville
- Filtre automatique des villes en fonction du pays sélectionné
- Option "Ma ville n'est pas dans la liste" pour saisir une ville personnalisée
- Champ `ville_naissance_custom` pour les villes non répertoriées

**Fichiers modifiés** :
- `frontend/pages/candidature/index.vue` : Ajout des champs et de la logique de filtrage
- `backend/routes/api.php` : Ajout de l'endpoint public `/public/pays`
- `backend/app/Models/Candidat.php` : Ajout des colonnes `pays_naissance_id` et `ville_naissance_custom` au fillable + relation `paysNaissance()`
- `backend/database/migrations/2026_06_17_120000_add_pays_naissance_to_candidats.php` : Nouvelle migration pour ajouter les colonnes

---

### ✅ 2. Récapitulatif Complet (Étape 3)

**Problème initial** : Le récapitulatif n'affichait que les informations de base, les champs facultatifs étaient absents.

**Solution implémentée** :
- Section "Photo d'identité" : Affichage de la photo uploadée
- Section "Informations personnelles" : 
  - NIP (si renseigné)
  - Matricule (si renseigné)
  - Nom complet
  - Date de naissance formatée en français
  - Genre (Masculin/Féminin)
  - Lieu de naissance avec pays (ville personnalisée ou sélectionnée)
  - État civil (si renseigné)
- Section "Coordonnées" :
  - Email
  - Téléphone (si renseigné)
  - Adresse (si renseignée)
- Section "Documents joints" :
  - Liste complète avec icônes emoji
  - Type de document avec badge coloré
  - Taille de fichier formatée

**Méthodes helper ajoutées** :
- `formatDateFr(dateString)` : Formate la date en français (ex: 15 mars 1990)
- `getLieuNaissanceLabel()` : Retourne la ville (custom ou sélectionnée)
- `getPaysNaissanceLabel()` : Retourne le nom du pays
- `getDocumentIcon(type)` : Retourne l'emoji correspondant au type de document
- `getDocumentTypeLabel(type)` : Retourne le label français du type de document

---

## 🔧 Modifications techniques détaillées

### Frontend (`pages/candidature/index.vue`)

#### Nouvelles variables reactive
```javascript
const paysListe = ref([])           // Liste des pays chargée depuis l'API
const showCustomVilleNaissance = ref(false)  // Toggle pour champ custom
```

#### Computed property
```javascript
const villesNaissanceFiltered = computed(() => {
  if (!form.value.pays_naissance_id) return []
  return villes.value.filter(ville => ville.pays_id == form.value.pays_naissance_id)
})
```

#### Nouvelle fonction
```javascript
const onPaysNaissanceChange = () => {
  form.value.lieu_naissance_id = ''
  form.value.ville_naissance_custom = ''
  showCustomVilleNaissance.value = false
}
```

#### Validation step 1 mise à jour
- Vérification de `pays_naissance_id` (obligatoire)
- Vérification que soit `lieu_naissance_id` soit `ville_naissance_custom` est rempli

---

### Backend

#### Nouvelle route API (`routes/api.php`)
```php
Route::get('/public/pays', [ReferentielController::class, 'pays']);
```

#### Nouvelle migration
**Fichier** : `database/migrations/2026_06_17_120000_add_pays_naissance_to_candidats.php`

**Colonnes ajoutées** :
- `pays_naissance_id` (integer, nullable, foreign key vers `pays`)
- `ville_naissance_custom` (varchar 100, nullable)

#### Modèle Candidat mis à jour
**Fillable** : Ajout de `pays_naissance_id` et `ville_naissance_custom`
**Relations** : Ajout de `paysNaissance()`

---

## 🚀 Étapes pour finaliser l'installation

### 1. Démarrer MAMP
- Ouvrir MAMP
- Démarrer Apache et MySQL
- Vérifier que MySQL est accessible sur le port 3306

### 2. Exécuter la migration
```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend
php artisan migrate --path=database/migrations/2026_06_17_120000_add_pays_naissance_to_candidats.php
```

OU pour exécuter toutes les migrations en attente :
```bash
php artisan migrate
```

### 3. Vérifier que les serveurs sont démarrés

**Backend Laravel** :
```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend
php artisan serve
```
URL: http://localhost:8000

**Frontend Nuxt** :
```bash
cd c:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\frontend
npm run dev
```
URL: http://localhost:3000

### 4. Tester le formulaire
- Accéder à http://localhost:3000/candidature
- Vérifier que le champ "Pays de naissance" s'affiche
- Sélectionner un pays et vérifier que seules les villes de ce pays s'affichent
- Tester le bouton "Ma ville n'est pas dans la liste"
- Compléter le formulaire jusqu'au récapitulatif (étape 3)
- Vérifier que TOUTES les informations (obligatoires et facultatives) s'affichent

---

## 📊 Structure de données attendue

### Objet form (après complétion)
```javascript
{
  nip: "123456",                    // Optionnel
  matricule: "MAT-001",             // Optionnel
  photo: "data:image/jpeg;base64,...",  // Optionnel (base64)
  pays_naissance_id: 1,             // OBLIGATOIRE
  lieu_naissance_id: 5,             // Obligatoire SI showCustomVilleNaissance = false
  ville_naissance_custom: "Ma Ville",   // Obligatoire SI showCustomVilleNaissance = true
  nom: "DUPONT",                    // OBLIGATOIRE
  prenom: "Jean",                   // OBLIGATOIRE
  date_naissance: "1990-03-15",     // OBLIGATOIRE
  genre: "M",                       // OBLIGATOIRE (M ou F)
  email: "jean.dupont@example.com", // OBLIGATOIRE
  telephone: "+241 01 23 45 67",    // Optionnel
  etat_civil: "Marié(e)",           // Optionnel
  adresse: "123 Rue Example",       // Optionnel
  password: "MonMotDePasse123",     // OBLIGATOIRE (min 8 caractères)
  password_confirmation: "MonMotDePasse123"  // OBLIGATOIRE (doit correspondre)
}
```

### Objet uploadedFiles
```javascript
[
  {
    file: File,                     // Objet File JavaScript
    name: "cv.pdf",
    size: 524288,                   // en bytes
    type: "cv"                      // cv, diplome, attestation, lettre, casier, medical, autre
  }
]
```

---

## 🎨 Design et UX

### Couleurs Gabonaises utilisées
- Vert : `gabon-green-600` (primary actions, success)
- Jaune : `gabon-yellow-600` (highlights)
- Bleu : `gabon-blue-600` (documents, info)

### Améliorations UX
1. **Progressive disclosure** : Les villes n'apparaissent qu'après sélection du pays
2. **Escape hatch** : Possibilité d'entrer une ville custom si absente de la liste
3. **Feedback visuel** : 
   - Barre de progression sur 3 étapes
   - Icônes SVG professionnelles (pas d'emoji dans les boutons)
   - Badges colorés pour les types de documents
4. **Validation en temps réel** : Messages d'erreur clairs via SweetAlert2
5. **Récapitulatif exhaustif** : Toutes les infos avant soumission

---

## 🐛 Bugs connus / Points d'attention

### ⚠️ Migration en attente
La migration `2026_06_17_120000_add_pays_naissance_to_candidats.php` doit être exécutée.

**Symptômes si non exécutée** :
- Erreur 500 lors de la soumission du formulaire
- Message "Column 'pays_naissance_id' not found" dans les logs Laravel

**Solution** : Voir section "Étapes pour finaliser l'installation" ci-dessus.

### ⚠️ Données de test nécessaires
Pour que le formulaire fonctionne, la base de données doit contenir :
- Des pays dans la table `pays`
- Des villes dans la table `ville` avec leur `pays_id` correspondant

**Vérification** :
```sql
SELECT COUNT(*) FROM pays;
SELECT COUNT(*) FROM ville;
SELECT v.nom as ville, p.nom as pays FROM ville v LEFT JOIN pays p ON v.pays_id = p.id LIMIT 10;
```

---

## 📝 Prochaines étapes suggérées

1. ✅ **Migration exécutée** : Ajouter les colonnes à la table candidats
2. ✅ **Tests manuels** : Vérifier le workflow complet d'inscription
3. 🔜 **Validation backend** : Ajouter des règles de validation pour `pays_naissance_id` et `ville_naissance_custom` dans `CandidatAuthController`
4. 🔜 **Enrichissement du dashboard candidat** : Afficher le pays et la ville de naissance
5. 🔜 **Admin - validation candidats** : Afficher le lieu de naissance complet (pays + ville) lors de la validation
6. 🔜 **Migration candidat → dignitaire** : Transférer le `pays_naissance_id` et `ville_naissance_custom` lors de la conversion

---

## 📚 Références

- **Documentation Laravel Migrations** : https://laravel.com/docs/10.x/migrations
- **Documentation Nuxt 3 Composables** : https://nuxt.com/docs/guide/directory-structure/composables
- **Documentation Vue 3 Computed** : https://vuejs.org/guide/essentials/computed.html
- **TailwindCSS Utilities** : https://tailwindcss.com/docs

---

**Date de modification** : 17 juin 2026  
**Auteur** : Kiro AI  
**Version** : 1.0
