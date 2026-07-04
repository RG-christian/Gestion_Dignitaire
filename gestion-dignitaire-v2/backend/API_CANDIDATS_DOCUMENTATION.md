# 📡 API Candidats & Conjoints - Documentation

**Date** : 17 juin 2026  
**Version** : 1.0  
**Base URL** : `http://localhost:8000/api`

---

## 🔐 Authentification

Toutes les routes protégées nécessitent un token Bearer dans le header :

```http
Authorization: Bearer {token}
```

---

## 📋 ROUTES CANDIDATS

### 🌐 Routes Publiques (Sans authentification)

#### 1️⃣ Inscription d'un candidat

```http
POST /api/candidats/register
Content-Type: application/json
```

**Body** :
```json
{
  "nom": "DOE",
  "prenom": "John",
  "date_naissance": "1990-05-15",
  "genre": "M",
  "email": "john.doe@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  
  // Optionnels
  "nip": "NIP123456",
  "matricule": "MAT789",
  "lieu_naissance_id": 1,
  "etat_civil": "Célibataire",
  "telephone": "+241 01 23 45 67",
  "adresse": "123 Rue de la Paix",
  "ville_residence_id": 5,
  "photo": "data:image/jpeg;base64,..."
}
```

**Réponse** (201) :
```json
{
  "success": true,
  "message": "Candidature enregistrée avec succès...",
  "token": "1|abcd1234...",
  "candidat": {
    "id": 1,
    "nom": "DOE",
    "prenom": "John",
    "statut": "en_attente",
    "nom_complet": "John DOE",
    "age": 36,
    "status_badge": {
      "text": "En attente",
      "color": "yellow"
    }
  }
}
```

---

#### 2️⃣ Connexion d'un candidat

```http
POST /api/candidats/login
Content-Type: application/json
```

**Body** :
```json
{
  "email": "john.doe@example.com",
  "password": "password123"
}
```

**Réponse** (200) :
```json
{
  "success": true,
  "message": "Connexion réussie",
  "token": "2|xyz789...",
  "candidat": { ... },
  "expires_at": "2026-06-24T10:00:00Z"
}
```

---

### 🔒 Routes Protégées (Candidat connecté)

#### 3️⃣ Profil du candidat connecté

```http
GET /api/candidats/me
Authorization: Bearer {token}
```

**Réponse** (200) :
```json
{
  "success": true,
  "candidat": {
    "id": 1,
    "statut": "en_attente",
    "nom": "DOE",
    "prenom": "John",
    "email": "john.doe@example.com",
    "date_candidature": "2026-06-17T10:00:00Z",
    "documents": [
      {
        "id": 1,
        "type_document": "cv",
        "nom_fichier": "cv_john_doe.pdf",
        "taille_lisible": "1.2 Mo"
      }
    ],
    "lieu_naissance": { ... },
    "ville_residence": { ... }
  }
}
```

---

#### 4️⃣ Modifier son profil

```http
PUT /api/candidats/me
Authorization: Bearer {token}
Content-Type: application/json
```

**Body** :
```json
{
  "telephone": "+241 07 77 77 77",
  "adresse": "Nouvelle adresse",
  "photo": "data:image/jpeg;base64,..."
}
```

**Note** : Seulement modifiable si `statut = 'en_attente'`

---

#### 5️⃣ Déconnexion

```http
POST /api/candidats/logout
Authorization: Bearer {token}
```

---

### 📄 Gestion des Documents

#### 6️⃣ Liste des documents

```http
GET /api/candidats/me/documents
Authorization: Bearer {token}
```

**Réponse** (200) :
```json
{
  "success": true,
  "documents": [
    {
      "id": 1,
      "type_document": "cv",
      "nom_fichier": "cv_john_doe.pdf",
      "taille_lisible": "1.2 Mo",
      "url_complete": "/storage/candidats/documents/abc123.pdf",
      "icone_type": {
        "icon": "📄",
        "color": "gray"
      },
      "uploaded_at": "2026-06-17T10:00:00Z"
    }
  ]
}
```

---

#### 7️⃣ Upload un document

```http
POST /api/candidats/me/documents
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Form Data** :
```
type_document: diplome | cv | lettre | attestation | casier | medical | passeport | autre
fichier: [FILE] (max 10 Mo, types: pdf, jpg, jpeg, png, doc, docx)
description: "Description optionnelle"
```

**Réponse** (201) :
```json
{
  "success": true,
  "message": "Document ajouté avec succès",
  "document": { ... }
}
```

---

#### 8️⃣ Supprimer un document

```http
DELETE /api/candidats/me/documents/{id}
Authorization: Bearer {token}
```

**Note** : Seulement possible si `statut = 'en_attente'`

---

## 👨‍💼 ROUTES ADMIN - Gestion des Candidatures

### 🔒 Toutes les routes admin nécessitent une authentification admin

#### 9️⃣ Liste des candidatures

```http
GET /api/admin/candidats?statut=en_attente&search=john&per_page=20
Authorization: Bearer {admin_token}
```

**Paramètres** :
- `statut` : en_attente | valide | refuse
- `genre` : M | F
- `search` : Recherche (nom, prénom, email, matricule, nip)
- `sort_by` : date_candidature (défaut)
- `sort_order` : desc | asc
- `per_page` : 20 (défaut)

---

#### 🔟 Statistiques des candidatures

```http
GET /api/admin/candidats/stats
Authorization: Bearer {admin_token}
```

**Réponse** (200) :
```json
{
  "success": true,
  "stats": {
    "total": 150,
    "en_attente": 25,
    "valides": 100,
    "refuses": 25,
    "hommes": 90,
    "femmes": 60,
    "derniere_semaine": 5,
    "ce_mois": 12
  }
}
```

---

#### 1️⃣1️⃣ Détail d'une candidature

```http
GET /api/admin/candidats/{id}
Authorization: Bearer {admin_token}
```

---

#### 1️⃣2️⃣ Valider une candidature

```http
POST /api/admin/candidats/{id}/valider
Authorization: Bearer {admin_token}
```

**Actions** :
1. Crée un dignitaire à partir du candidat
2. Change le statut du candidat à "valide"
3. Enregistre l'admin qui a validé
4. TODO: Envoie un email de confirmation au candidat

**Réponse** (200) :
```json
{
  "success": true,
  "message": "Candidature validée avec succès...",
  "candidat": { ... },
  "dignitaire": {
    "id": 123,
    "nom": "DOE",
    "prenom": "John",
    ...
  }
}
```

---

#### 1️⃣3️⃣ Refuser une candidature

```http
POST /api/admin/candidats/{id}/refuser
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Body** :
```json
{
  "motif": "Dossier incomplet. Veuillez fournir votre diplôme et votre casier judiciaire."
}
```

**Réponse** (200) :
```json
{
  "success": true,
  "message": "Candidature refusée",
  "candidat": {
    "statut": "refuse",
    "motif_refus": "Dossier incomplet..."
  }
}
```

---

#### 1️⃣4️⃣ Télécharger un document

```http
GET /api/admin/candidats/{candidatId}/documents/{documentId}/download
Authorization: Bearer {admin_token}
```

**Réponse** : Téléchargement du fichier

---

#### 1️⃣5️⃣ Supprimer une candidature

```http
DELETE /api/admin/candidats/{id}
Authorization: Bearer {admin_token}
```

---

## 👨‍👩‍👧 ROUTES CONJOINTS

### 🔒 Toutes les routes nécessitent une authentification

#### 1️⃣6️⃣ Liste des conjoints d'un dignitaire

```http
GET /api/dignitaires/{dignitaireId}/conjoints
Authorization: Bearer {token}
```

**Réponse** (200) :
```json
{
  "success": true,
  "conjoints": [
    {
      "id": 1,
      "nom": "SMITH",
      "prenom": "Jane",
      "genre": "F",
      "statut": "actif",
      "est_militaire": false,
      "est_dignitaire": true,
      "fonction_dignitaire": "Ministre",
      "nom_complet": "Jane SMITH",
      "age": 35,
      "duree_mariage": "5 ans et 3 mois",
      "status_badge": {
        "text": "Actif",
        "color": "green"
      }
    }
  ]
}
```

---

#### 1️⃣7️⃣ Ajouter un conjoint

```http
POST /api/dignitaires/{dignitaireId}/conjoints
Authorization: Bearer {token}
Content-Type: application/json
```

**Body** :
```json
{
  "nom": "SMITH",
  "prenom": "Jane",
  "genre": "F",
  "date_naissance": "1991-03-20",
  "lieu_naissance_id": 10,
  "nationalite_id": 5,
  "profession": "Médecin",
  "employeur": "CHU de Libreville",
  "date_mariage": "2020-06-15",
  "lieu_mariage": "Libreville",
  "statut": "actif",
  
  // Statut spécial (recommandation Marcel)
  "est_militaire": false,
  "est_dignitaire": true,
  "fonction_dignitaire": "Ministre de la Santé",
  
  // Coordonnées
  "telephone": "+241 06 12 34 56",
  "email": "jane.smith@example.com",
  "adresse": "Quartier Batterie IV"
}
```

---

#### 1️⃣8️⃣ Détail d'un conjoint

```http
GET /api/conjoints/{id}
Authorization: Bearer {token}
```

---

#### 1️⃣9️⃣ Modifier un conjoint

```http
PUT /api/conjoints/{id}
Authorization: Bearer {token}
Content-Type: application/json
```

---

#### 2️⃣0️⃣ Terminer une union

```http
POST /api/conjoints/{id}/terminer-union
Authorization: Bearer {token}
Content-Type: application/json
```

**Body** :
```json
{
  "nouveau_statut": "divorce",
  "date_fin": "2025-12-31"
}
```

**Valeurs possibles** : `divorce`, `veuf`, `separe`

---

#### 2️⃣1️⃣ Supprimer un conjoint

```http
DELETE /api/conjoints/{id}
Authorization: Bearer {token}
```

---

## 🔄 Flux Complet - Candidature

```
1. Candidat s'inscrit
   POST /api/candidats/register
   → statut = "en_attente"

2. Candidat se connecte
   POST /api/candidats/login
   → Reçoit un token

3. Candidat upload documents
   POST /api/candidats/me/documents
   → CV, diplômes, attestations

4. Admin reçoit notification (TODO)
   → Email ou notification système

5. Admin consulte la candidature
   GET /api/admin/candidats/{id}
   → Voir tous les documents

6a. Admin valide
    POST /api/admin/candidats/{id}/valider
    → Crée le dignitaire
    → statut = "valide"

6b. Admin refuse
    POST /api/admin/candidats/{id}/refuser
    → statut = "refuse"
    → Enregistre le motif

7. Candidat reçoit email (TODO)
   → Confirmation ou refus
```

---

## ✅ Codes de Réponse HTTP

| Code | Signification |
|------|---------------|
| 200 | Succès |
| 201 | Ressource créée |
| 400 | Requête invalide |
| 401 | Non authentifié |
| 403 | Accès refusé |
| 404 | Ressource introuvable |
| 422 | Erreur de validation |
| 500 | Erreur serveur |

---

## 🧪 Tests avec Postman/Insomnia

### Collection recommandée :

1. **Candidats Public**
   - Register
   - Login

2. **Candidats Privé**
   - Get Profile
   - Update Profile
   - Upload Document
   - Delete Document
   - Logout

3. **Admin - Candidatures**
   - List Candidats
   - Get Stats
   - Show Candidat
   - Valider Candidature
   - Refuser Candidature
   - Delete Candidat

4. **Conjoints**
   - List Conjoints
   - Add Conjoint
   - Update Conjoint
   - Terminer Union
   - Delete Conjoint

---

## 🚀 Prochaines étapes

1. ✅ Créer les pages Frontend (Nuxt)
2. ✅ Implémenter les notifications email
3. ✅ Ajouter la traçabilité (audit logs)
4. ✅ Tests automatisés (PHPUnit)

---

**Statut** : ✅ API complète et prête à l'emploi
