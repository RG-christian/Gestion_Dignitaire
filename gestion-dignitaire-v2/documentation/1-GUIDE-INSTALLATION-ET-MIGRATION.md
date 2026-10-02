# 📦 Guide d'Installation et de Migration

Ce document regroupe toutes les informations nécessaires pour installer, configurer et migrer le système de gestion des dignitaires.

---

## 🚀 Installation Initiale

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Composer (optionnel mais recommandé)

### Étapes d'installation

#### 1. Cloner le projet
```bash
git clone <url-du-repo>
cd gestion_dignitaire
```

#### 2. Configurer l'environnement
```bash
cp .env.example .env
```
Éditer le fichier `.env` avec vos paramètres de base de données.

#### 3. Installer les dépendances
```bash
composer install
```

#### 4. Créer la base de données
```sql
CREATE DATABASE gestion_dignitaire CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 5. Exécuter les migrations
```bash
php run_migrations.php
```

#### 6. Configurer les permissions
```bash
chmod 755 uploads/
chmod 755 logs/
```

---

## 🔄 Plan de Migration vers Laravel + Inertia + Vue 3

### 🎯 Objectif
Migrer l'application PHP vanilla vers une stack moderne Laravel + Inertia.js + Vue 3

### 📊 Estimation
- **Durée totale** : 3-4 semaines
- **Effort** : ~120-160 heures
- **Risque** : Faible (migration progressive possible)

---

### 🗓️ Phase 1 : Préparation (3-4 jours)

#### Jour 1-2 : Installation Laravel
```bash
# Créer un nouveau projet Laravel
composer create-project laravel/laravel gestion-dignitaire-laravel

# Installer les dépendances
cd gestion-dignitaire-laravel
composer require laravel/breeze
php artisan breeze:install vue --ssr

# Installer Inertia
npm install
```

#### Jour 3 : Configuration
```bash
# Copier le .env
cp ../gestion_dignitaire/.env .env

# Configurer la base de données dans .env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=gestion_dignitaire
DB_USERNAME=root
DB_PASSWORD=root
```

#### Jour 4 : Migration de la base de données
```bash
# Générer les migrations depuis la base existante
composer require --dev kitloong/laravel-migrations-generator
php artisan migrate:generate

# Ou créer manuellement
php artisan make:migration create_dignitaires_table
```

**Exemple de migration Laravel :**
```php
// database/migrations/xxxx_create_dignitaires_table.php
public function up()
{
    Schema::create('dignitaires', function (Blueprint $table) {
        $table->id();
        $table->string('nip', 20)->unique()->nullable();
        $table->string('matricule', 20)->unique();
        $table->string('nom', 100)->nullable();
        $table->string('prenom', 100)->nullable();
        $table->date('date_naissance')->nullable();
        $table->foreignId('lieu_naissance')->nullable()
              ->constrained('villes')->nullOnDelete();
        $table->string('nationalite', 100)->nullable();
        $table->enum('genre', ['M', 'F'])->nullable();
        $table->string('etat_civil', 20)->nullable();
        $table->string('photo')->nullable();
        $table->string('telephone', 20)->nullable();
        $table->string('adresse')->nullable();
        $table->string('casierJud')->nullable();
        $table->string('certificatsMed')->nullable();
        $table->timestamps();
        $table->softDeletes();
        
        $table->index(['nom', 'prenom']);
    });
}
```

---

### 🗓️ Phase 2 : Modèles et Relations (4-5 jours)

#### Créer les modèles Eloquent

```bash
php artisan make:model Dignitaire
php artisan make:model Diplome
php artisan make:model Enfant
php artisan make:model Decoration
php artisan make:model Nomination
php artisan make:model Poste
php artisan make:model Experience
php artisan make:model Ville
php artisan make:model Pays
php artisan make:model Region
```

#### Exemple : Modèle Dignitaire

```php
// app/Models/Dignitaire.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dignitaire extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nip', 'matricule', 'nom', 'prenom', 'date_naissance',
        'lieu_naissance', 'nationalite', 'genre', 'etat_civil',
        'photo', 'telephone', 'adresse', 'casierJud', 'certificatsMed'
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    // Relations
    public function diplomes(): HasMany
    {
        return $this->hasMany(Diplome::class);
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(Enfant::class);
    }

    public function decorations(): BelongsToMany
    {
        return $this->belongsToMany(Decoration::class, 'decoration_dignitaire')
                    ->withPivot('date_attribution')
                    ->withTimestamps();
    }

    public function nominations(): HasMany
    {
        return $this->hasMany(Nomination::class);
    }

    public function postes(): HasMany
    {
        return $this->hasMany(Poste::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class);
    }

    public function lieuNaissance()
    {
        return $this->belongsTo(Ville::class, 'lieu_naissance');
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('matricule', 'like', "%{$search}%");
    }
}
```

---

### 🗓️ Phase 3 : Authentification (2-3 jours)

#### Laravel Breeze est déjà installé !

```bash
# Créer un seeder pour l'admin
php artisan make:seeder AdminSeeder
```

```php
// database/seeders/AdminSeeder.php
public function run()
{
    User::create([
        'name' => 'Administrateur',
        'email' => 'admin@gestion-dignitaire.com',
        'password' => Hash::make('password'),
        'role' => 'admin'
    ]);
}
```

#### Middleware de rôles

```bash
php artisan make:middleware CheckRole
```

```php
// app/Http/Middleware/CheckRole.php
public function handle($request, Closure $next, $role)
{
    if (!auth()->check() || auth()->user()->role !== $role) {
        abort(403, 'Accès non autorisé');
    }
    return $next($request);
}
```

---

### 🗓️ Phase 4 : Contrôleurs et Routes (5-6 jours)

#### Créer les contrôleurs

```bash
php artisan make:controller DignitaireController --resource
php artisan make:controller DiplomeController --resource
php artisan make:controller DecorationController --resource
```

#### Exemple : DignitaireController

```php
// app/Http/Controllers/DignitaireController.php
namespace App\Http\Controllers;

use App\Models\Dignitaire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DignitaireController extends Controller
{
    public function index(Request $request)
    {
        $dignitaires = Dignitaire::query()
            ->when($request->search, function($query, $search) {
                $query->search($search);
            })
            ->with(['lieuNaissance', 'diplomes', 'decorations'])
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Dignitaires/Index', [
            'dignitaires' => $dignitaires,
            'filters' => $request->only('search')
        ]);
    }

    public function show(Dignitaire $dignitaire)
    {
        $dignitaire->load([
            'diplomes.etablissement',
            'enfants',
            'decorations',
            'nominations.entite',
            'postes',
            'experiences'
        ]);

        return Inertia::render('Dignitaires/Show', [
            'dignitaire' => $dignitaire
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|unique:dignitaires|max:20',
            'matricule' => 'required|unique:dignitaires|max:20',
            'nom' => 'required|max:100',
            'prenom' => 'required|max:100',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|exists:villes,id',
            'nationalite' => 'nullable|max:100',
            'genre' => 'nullable|in:M,F',
            'etat_civil' => 'nullable|in:Célibataire,Marié(e),Divorcé(e),Veuf(ve)',
            'telephone' => 'nullable|regex:/^[0-9+\-\s()]+$/',
            'photo' => 'nullable|image|max:5120', // 5MB
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $dignitaire = Dignitaire::create($validated);

        return redirect()->route('dignitaires.show', $dignitaire)
                        ->with('success', 'Dignitaire créé avec succès');
    }

    public function update(Request $request, Dignitaire $dignitaire)
    {
        $validated = $request->validate([
            'nip' => 'nullable|unique:dignitaires,nip,'.$dignitaire->id.'|max:20',
            'matricule' => 'required|unique:dignitaires,matricule,'.$dignitaire->id.'|max:20',
            'nom' => 'required|max:100',
            'prenom' => 'required|max:100',
            // ... autres règles
        ]);

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo
            if ($dignitaire->photo) {
                Storage::disk('public')->delete($dignitaire->photo);
            }
            $validated['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $dignitaire->update($validated);

        return redirect()->route('dignitaires.show', $dignitaire)
                        ->with('success', 'Dignitaire modifié avec succès');
    }

    public function destroy(Dignitaire $dignitaire)
    {
        $dignitaire->delete();

        return redirect()->route('dignitaires.index')
                        ->with('success', 'Dignitaire supprimé avec succès');
    }
}
```

#### Routes

```php
// routes/web.php
use App\Http\Controllers\DignitaireController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('dignitaires', DignitaireController::class);
    Route::resource('diplomes', DiplomeController::class);
    Route::resource('decorations', DecorationController::class);
    Route::resource('nominations', NominationController::class);
    
    // Routes personnalisées
    Route::get('dignitaires/{dignitaire}/diplomes', [DiplomeController::class, 'byDignitaire'])
         ->name('dignitaires.diplomes');
});
```

---

### 📊 Comparaison Avant/Après

| Aspect | PHP Vanilla | Laravel + Vue |
|--------|-------------|---------------|
| **Lignes de code** | ~5000 | ~2000 (-60%) |
| **Sécurité** | Manuelle | Intégrée |
| **Validation** | Custom | Built-in |
| **ORM** | DAO manuel | Eloquent |
| **Tests** | Aucun | PHPUnit intégré |
| **UI** | PHP templates | Vue.js réactif |
| **Maintenance** | Difficile | Facile |
| **Performance** | Moyenne | Excellente |
| **Évolutivité** | Limitée | Excellente |

---

## 💰 Coût vs Bénéfices

### Coûts
- **Temps** : 3-4 semaines
- **Formation** : 1 semaine Laravel + 1 semaine Vue.js
- **Tests** : 1 semaine

### Bénéfices
- **Maintenance** : -70% de temps
- **Bugs** : -80% (grâce aux tests)
- **Nouvelles features** : 3x plus rapide
- **Sécurité** : Niveau entreprise
- **Performance** : 2-3x plus rapide
- **Expérience utilisateur** : Moderne et fluide

---

## 🎯 Recommandation finale

**OUI, migrez vers Laravel + Inertia + Vue 3**

C'est un investissement qui sera rentabilisé en 6 mois maximum grâce à :
- Développement plus rapide
- Moins de bugs
- Maintenance simplifiée
- Code moderne et maintenable
- Équipe plus motivée (stack moderne)

---

## 🔒 Sécurité

### Fonctionnalités de sécurité implémentées

✅ **Protection CSRF** : Tous les formulaires sont protégés par des tokens CSRF  
✅ **Sessions sécurisées** : Régénération d'ID après login, timeout automatique  
✅ **Validation des données** : Toutes les entrées utilisateur sont validées et sanitizées  
✅ **Requêtes préparées** : Protection contre les injections SQL  
✅ **Hachage des mots de passe** : Utilisation de `password_hash()` et `password_verify()`  
✅ **Routeur sécurisé** : Whitelist des contrôleurs autorisés  
✅ **Upload sécurisé** : Validation des types MIME, génération de noms aléatoires  
✅ **Logging** : Traçabilité des actions importantes

### Checklist Sécurité Développeurs

#### Pour chaque formulaire
```php
// Dans la vue
<?php require_once 'config/security.php'; ?>
<form method="post" action="">
    <?= csrfField() ?>
    <!-- Vos champs -->
</form>

// Dans le contrôleur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Vérifier le token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
        die('Token CSRF invalide');
    }
    
    // 2. Valider les données
    $validator = new Validator($_POST);
    $validator->required('nom')->minLength('nom', 3);
    
    // 3. Sanitizer les données
    $nom = Validator::sanitize($_POST['nom']);
}
```

#### Vulnérabilités courantes à éviter

**SQL Injection** :
```php
// ❌ VULNÉRABLE
$sql = "SELECT * FROM users WHERE username = '$username'";

// ✅ SÉCURISÉ
$sql = "SELECT * FROM users WHERE username = ?";
$stmt->execute([$username]);
```

**XSS (Cross-Site Scripting)** :
```php
// ❌ VULNÉRABLE
echo "<div>$userComment</div>";

// ✅ SÉCURISÉ
echo "<div>" . htmlspecialchars($userComment, ENT_QUOTES, 'UTF-8') . "</div>";
```

**Upload de fichiers malveillants** :
```php
// ❌ VULNÉRABLE
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);

// ✅ SÉCURISÉ
$uploader = new FileUploader(
    'uploads/photos/',
    ['jpg', 'jpeg', 'png'],
    5242880 // 5MB max
);
$result = $uploader->upload($_FILES['photo']);
```

### Configuration de production

Pour un environnement de production, modifier dans `.env` :
```
APP_ENV=production
APP_DEBUG=false
```

**Apache (.htaccess)** :
```apache
# Désactiver l'affichage des erreurs
php_flag display_errors off

# Protéger les fichiers sensibles
<FilesMatch "^\.env$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Headers de sécurité
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
```

**PHP (php.ini)** :
```ini
display_errors = Off
log_errors = On
error_log = /path/to/logs/php-error.log

# Désactiver les fonctions dangereuses
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

# Limiter les uploads
upload_max_filesize = 5M
post_max_size = 6M

# Sessions sécurisées
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

---

## 📁 Structure du projet

```
.
├── classes/              # Classes métier et DAO
├── config/              # Configuration (DB, sécurité, validation)
├── controllers/         # Contrôleurs MVC
├── migrations/          # Scripts de migration de base de données
├── routers/            # Système de routage
├── uploads/            # Fichiers uploadés (photos, documents)
├── views/              # Vues (templates)
├── logs/               # Fichiers de logs
├── .env                # Configuration environnement (ne pas commiter)
└── index.php           # Point d'entrée
```

---

**Date de création** : Octobre 2026  
**Dernière mise à jour** : 1er octobre 2026  
**Statut** : Documentation consolidée
