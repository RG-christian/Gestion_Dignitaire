# Plan de Migration vers Laravel + Inertia + Vue 3

## 🎯 Objectif
Migrer l'application PHP vanilla vers une stack moderne Laravel + Inertia.js + Vue 3

## 📊 Estimation
- **Durée totale** : 3-4 semaines
- **Effort** : ~120-160 heures
- **Risque** : Faible (migration progressive possible)

---

## 🗓️ Phase 1 : Préparation (3-4 jours)

### Jour 1-2 : Installation Laravel
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

### Jour 3 : Configuration
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

### Jour 4 : Migration de la base de données
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

## 🗓️ Phase 2 : Modèles et Relations (4-5 jours)

### Créer les modèles Eloquent

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

### Exemple : Modèle Dignitaire

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

## 🗓️ Phase 3 : Authentification (2-3 jours)

### Laravel Breeze est déjà installé !

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

### Middleware de rôles

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

## 🗓️ Phase 4 : Contrôleurs et Routes (5-6 jours)

### Créer les contrôleurs

```bash
php artisan make:controller DignitaireController --resource
php artisan make:controller DiplomeController --resource
php artisan make:controller DecorationController --resource
```

### Exemple : DignitaireController

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

### Routes

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

## 🗓️ Phase 5 : Vues Vue.js avec Inertia (7-8 jours)

### Structure des composants

```
resources/js/
├── Pages/
│   ├── Auth/
│   │   ├── Login.vue
│   │   └── Register.vue
│   ├── Dignitaires/
│   │   ├── Index.vue
│   │   ├── Show.vue
│   │   ├── Create.vue
│   │   └── Edit.vue
│   ├── Diplomes/
│   ├── Decorations/
│   └── Dashboard.vue
├── Components/
│   ├── Layout/
│   │   ├── AppLayout.vue
│   │   ├── Navbar.vue
│   │   └── Sidebar.vue
│   ├── Forms/
│   │   ├── Input.vue
│   │   ├── Select.vue
│   │   └── FileUpload.vue
│   └── Tables/
│       ├── DataTable.vue
│       └── Pagination.vue
└── app.js
```

### Exemple : Index des Dignitaires

```vue
<!-- resources/js/Pages/Dignitaires/Index.vue -->
<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Components/Layout/AppLayout.vue';
import DataTable from '@/Components/Tables/DataTable.vue';
import Pagination from '@/Components/Tables/Pagination.vue';

const props = defineProps({
    dignitaires: Object,
    filters: Object
});

const search = ref(props.filters.search || '');

const searchDignitaires = () => {
    router.get('/dignitaires', { search: search.value }, {
        preserveState: true,
        replace: true
    });
};

const columns = [
    { key: 'matricule', label: 'Matricule' },
    { key: 'nom', label: 'Nom' },
    { key: 'prenom', label: 'Prénom' },
    { key: 'telephone', label: 'Téléphone' },
    { key: 'actions', label: 'Actions' }
];
</script>

<template>
    <AppLayout title="Gestion des Dignitaires">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold">Liste des Dignitaires</h2>
                            <Link 
                                :href="route('dignitaires.create')"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                            >
                                Ajouter un dignitaire
                            </Link>
                        </div>

                        <!-- Recherche -->
                        <div class="mb-4">
                            <input
                                v-model="search"
                                @input="searchDignitaires"
                                type="text"
                                placeholder="Rechercher par nom, prénom ou matricule..."
                                class="w-full px-4 py-2 border rounded-lg"
                            />
                        </div>

                        <!-- Table -->
                        <DataTable :columns="columns" :data="dignitaires.data">
                            <template #cell-nom="{ row }">
                                <Link 
                                    :href="route('dignitaires.show', row.id)"
                                    class="text-blue-600 hover:underline"
                                >
                                    {{ row.nom }}
                                </Link>
                            </template>

                            <template #cell-actions="{ row }">
                                <div class="flex gap-2">
                                    <Link 
                                        :href="route('dignitaires.edit', row.id)"
                                        class="text-blue-600 hover:underline"
                                    >
                                        Modifier
                                    </Link>
                                    <button 
                                        @click="deleteDignitaire(row.id)"
                                        class="text-red-600 hover:underline"
                                    >
                                        Supprimer
                                    </button>
                                </div>
                            </template>
                        </DataTable>

                        <!-- Pagination -->
                        <Pagination :links="dignitaires.links" class="mt-4" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
```

---

## 🗓️ Phase 6 : Tests et Déploiement (3-4 jours)

### Tests automatisés

```bash
php artisan make:test DignitaireTest
```

```php
// tests/Feature/DignitaireTest.php
public function test_can_create_dignitaire()
{
    $user = User::factory()->create(['role' => 'admin']);
    
    $response = $this->actingAs($user)->post('/dignitaires', [
        'matricule' => 'MAT001',
        'nom' => 'Dupont',
        'prenom' => 'Jean',
        'genre' => 'M'
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('dignitaires', ['matricule' => 'MAT001']);
}
```

### Déploiement

```bash
# Build des assets
npm run build

# Optimisation Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Migration en production
php artisan migrate --force
```

---

## 📊 Comparaison Avant/Après

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
