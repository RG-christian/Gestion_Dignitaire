# 🛠️ Fix : Impossible d'ajouter diplômes/expériences côté candidat

## 🔴 Problème identifié

Erreur **422 Unprocessable Content** lors de l'ajout de diplômes ou expériences depuis le dashboard candidat.

---

## ✅ SOLUTION

### Étape 1 : Vérifier que les tables existent

Connectez-vous à MySQL via phpMyAdmin ou ligne de commande et vérifiez :

```sql
SHOW TABLES LIKE 'candidat_diplomes';
SHOW TABLES LIKE 'candidat_experiences';
```

Si elles n'existent pas, **exécutez les migrations** :

### Étape 2 : Exécuter les migrations manquantes

**Dans PowerShell, allez dans le dossier backend** :

```powershell
cd C:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend
```

**Exécutez les migrations** :

```powershell
php artisan migrate
```

Ou si vous avez des migrations en attente :

```powershell
php artisan migrate:status
php artisan migrate --path=database/migrations/2026_07_01_100000_create_candidat_diplomes_table.php
php artisan migrate --path=database/migrations/2026_07_01_100002_create_candidat_experiences_table.php
```

---

### Étape 3 : Vérifier les relations dans le modèle Candidat

Ouvrir `backend/app/Models/Candidat.php` et vérifier que les relations existent :

```php
public function diplomes(): HasMany
{
    return $this->hasMany(CandidatDiplome::class);
}

public function experiences(): HasMany
{
    return $this->hasMany(CandidatExperience::class);
}
```

✅ **Déjà présent** (vérifié)

---

### Étape 4 : Vérifier le guard d'authentification candidat

Ouvrir `backend/config/auth.php` et vérifier :

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'sanctum' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
    'candidat' => [
        'driver' => 'sanctum',
        'provider' => 'candidats',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],
    'candidats' => [
        'driver' => 'eloquent',
        'model' => App\Models\Candidat::class,
    ],
],
```

---

### Étape 5 : Vérifier le middleware dans api.php

Ouvrir `backend/routes/api.php` et vérifier que les routes candidat utilisent bien le guard `candidat` :

```php
Route::prefix('candidats')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [CandidatAuthController::class, 'logout']);
    Route::get('/me', [CandidatAuthController::class, 'me']);
    
    // Diplômes
    Route::get('/me/diplomes', [CandidatDiplomeController::class, 'index']);
    Route::post('/me/diplomes', [CandidatDiplomeController::class, 'store']);
    Route::delete('/me/diplomes/{id}', [CandidatDiplomeController::class, 'destroy']);
    
    // Expériences
    Route::get('/me/experiences', [CandidatExperienceController::class, 'index']);
    Route::post('/me/experiences', [CandidatExperienceController::class, 'store']);
    Route::delete('/me/experiences/{id}', [CandidatExperienceController::class, 'destroy']);
});
```

✅ **Déjà configuré** (vérifié)

---

### Étape 6 : Vérifier les logs Laravel

**Consulter les logs Laravel** pour voir l'erreur exacte :

```powershell
cd C:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2\backend
Get-Content storage\logs\laravel.log -Tail 50
```

Ou ouvrir le fichier `backend/storage/logs/laravel.log` dans un éditeur.

---

### Étape 7 : Tester les routes API directement

**Test avec curl ou Postman** :

```bash
# 1. Login candidat pour obtenir token
curl -X POST http://localhost:8000/api/candidats/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# Copier le token retourné

# 2. Ajouter un diplôme
curl -X POST http://localhost:8000/api/candidats/me/diplomes \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -F "intitule=Licence en Informatique" \
  -F "annee=2020"
```

---

## 🎯 Causes probables du problème

| Cause | Probabilité | Solution |
|---|---|---|
| **Tables non créées** (migrations non exécutées) | 90% | Exécuter `php artisan migrate` |
| **Token candidat invalide** | 5% | Vérifier `localStorage.getItem('candidat_token')` |
| **Guard d'authentification mal configuré** | 3% | Vérifier `config/auth.php` |
| **Validation échouée** (champs manquants) | 2% | Consulter `backend/storage/logs/laravel.log` |

---

## ✅ Après correction

1. **Rafraîchir la page** du dashboard candidat (Ctrl+Shift+R)
2. **Essayer d'ajouter un diplôme** avec tous les champs requis
3. **Vérifier dans la console** que l'appel API retourne 201 Created

---

**Fichier créé par** : Kiro AI  
**Date** : 17 juin 2026
