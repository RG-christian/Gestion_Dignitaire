# 📋 Liste Complète des Fichiers à Créer

## ✅ Fichiers Déjà Créés

### Backend
- ✅ `backend/README.md`
- ✅ `backend/.env.example`
- ✅ `backend/composer.json`
- ✅ `backend/database/migrations/2024_01_01_000001_create_base_tables.php`
- ✅ `backend/database/migrations/2024_01_01_000002_create_dignitaires_table.php`
- ✅ `backend/database/migrations/2024_01_01_000003_create_related_tables.php`
- ✅ `backend/app/Models/Dignitaire.php`
- ✅ `backend/app/Http/Controllers/Api/DignitaireController.php`
- ✅ `backend/routes/api.php`

### Frontend
- ✅ `frontend/package.json`
- ✅ `frontend/nuxt.config.ts`
- ✅ `frontend/.env.example`
- ✅ `frontend/composables/useApi.ts`
- ✅ `frontend/stores/auth.ts`
- ✅ `frontend/pages/index.vue`
- ✅ `frontend/pages/dignitaires/index.vue`

### Documentation
- ✅ `INSTALLATION.md`
- ✅ `MODELS_COMPLETS.md`

---

## 🔨 Fichiers Restants à Créer

### Backend Laravel (dans `backend/`)

#### 1. Models (app/Models/)
```bash
# Copier le contenu depuis MODELS_COMPLETS.md
touch app/Models/Nomination.php
touch app/Models/Decoration.php
touch app/Models/Ville.php
touch app/Models/Pays.php
touch app/Models/Region.php
touch app/Models/Entite.php
touch app/Models/Poste.php
touch app/Models/Diplome.php
touch app/Models/Enfant.php
touch app/Models/Experience.php
touch app/Models/LangueParlee.php
touch app/Models/Langue.php
touch app/Models/Domaine.php
touch app/Models/Structure.php
touch app/Models/Etablissement.php
touch app/Models/Pv.php
```

#### 2. Controllers (app/Http/Controllers/Api/)
```bash
touch app/Http/Controllers/Api/AuthController.php
touch app/Http/Controllers/Api/NominationController.php
touch app/Http/Controllers/Api/DecorationController.php
touch app/Http/Controllers/Api/PosteController.php
touch app/Http/Controllers/Api/ReferentielController.php
```

**Contenu de NominationController.php :**
```php
<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Nomination;
use Illuminate\Http\Request;

class NominationController extends Controller
{
    public function index(Request $request)
    {
        $nominations = Nomination::with(['dignitaire', 'entite', 'poste', 'pv'])
            ->latest()
            ->paginate(20);
        
        return response()->json($nominations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaires,id',
            'entite_id' => 'nullable|exists:entites,id',
            'poste_id' => 'nullable|exists:postes,id',
            'pv_id' => 'nullable|exists:pvs,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date',
            'fonction' => 'nullable|string|max:150',
        ]);

        $nomination = Nomination::create($validated);
        return response()->json($nomination, 201);
    }

    public function update(Request $request, $id)
    {
        $nomination = Nomination::findOrFail($id);
        
        $validated = $request->validate([
            'dignitaire_id' => 'required|exists:dignitaires,id',
            'entite_id' => 'nullable|exists:entites,id',
            'poste_id' => 'nullable|exists:postes,id',
            'pv_id' => 'nullable|exists:pvs,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date',
            'fonction' => 'nullable|string|max:150',
        ]);

        $nomination->update($validated);
        return response()->json($nomination);
    }

    public function destroy($id)
    {
        $nomination = Nomination::findOrFail($id);
        $nomination->delete();
        return response()->json(['message' => 'Nomination supprimée']);
    }
}
```

**Contenu de ReferentielController.php :**
```php
<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Pays, Ville, Entite, Langue, Domaine, Structure, Etablissement};

class ReferentielController extends Controller
{
    public function pays() {
        return response()->json(Pays::with('region')->orderBy('nom')->get());
    }

    public function regions() {
        return response()->json(\App\Models\Region::orderBy('nom')->get());
    }

    public function villes() {
        return response()->json(Ville::with('pays')->orderBy('nom')->get());
    }

    public function entites() {
        return response()->json(Entite::orderBy('nom')->get());
    }

    public function langues() {
        return response()->json(Langue::orderBy('nom')->get());
    }

    public function domaines() {
        return response()->json(Domaine::orderBy('nom')->get());
    }

    public function structures() {
        return response()->json(Structure::orderBy('nom')->get());
    }

    public function etablissements() {
        return response()->json(Etablissement::orderBy('nom')->get());
    }
}
```

#### 3. Seeders (database/seeders/)
```bash
touch database/seeders/DatabaseSeeder.php
touch database/seeders/UserSeeder.php
touch database/seeders/ReferentielSeeder.php
```

#### 4. Configuration
```bash
# Modifier config/cors.php pour autoriser Nuxt
# Modifier config/sanctum.php pour les domaines stateful
```

---

### Frontend Nuxt (dans `frontend/`)

#### 1. Composants (components/)
```bash
mkdir -p components
touch components/DashboardLayout.vue
touch components/DignitaireCard.vue
touch components/DignitaireModal.vue
touch components/StatCard.vue
touch components/Pagination.vue
touch components/NominationModal.vue
touch components/DecorationModal.vue
```

**Contenu de StatCard.vue :**
```vue
<template>
  <div class="bg-white rounded-xl shadow border p-5 flex items-center">
    <div class="flex-1">
      <div class="text-gray-500 mb-1">{{ title }}</div>
      <div class="text-2xl font-bold">{{ value }}</div>
    </div>
    <div class="ml-3">
      <span :class="`inline-flex bg-${color}-200 p-3 rounded-full`">
        <svg class="w-7 h-7" :class="`text-${color}-600`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath" />
        </svg>
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title: string
  value: number
  icon: string
  color: string
}>()

const iconPath = computed(() => {
  const icons = {
    users: 'M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5m6-6a4 4 0 10-8 0 4 4 0 008 0z',
    briefcase: 'M6 7V6a2 2 0 012-2h8a2 2 0 012 2v1M6 7h12M6 7v12a2 2 0 002 2h8a2 2 0 002-2V7',
    medal: 'M12 8v8m0 0a4 4 0 110-8 4 4 0 010 8z',
    map: 'M3 21V3h18v18M3 21v-6h18v6'
  }
  return icons[props.icon] || icons.users
})
</script>
```

**Contenu de DignitaireCard.vue :**
```vue
<template>
  <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col items-center group relative">
    <img 
      :src="`/uploads/photos/${dignitaire.photo}`" 
      :alt="dignitaire.nom_complet"
      class="w-24 h-24 rounded-full object-cover border-4 border-green-200 shadow mb-2"
    />
    <h4 class="text-base font-semibold mb-0.5">{{ dignitaire.nom_complet }}</h4>
    <p class="text-sm text-gray-600">{{ dignitaire.matricule }}</p>
    
    <!-- Actions au hover -->
    <div class="absolute top-3 right-3 flex flex-col space-y-2 opacity-0 group-hover:opacity-100 transition">
      <NuxtLink 
        :to="`/dignitaires/${dignitaire.id}`"
        class="bg-sky-100 hover:bg-sky-200 text-sky-800 p-1.5 rounded-full shadow"
      >
        👁️
      </NuxtLink>
      <button 
        @click="$emit('edit', dignitaire)"
        class="bg-blue-100 hover:bg-blue-200 text-blue-800 p-1.5 rounded-full shadow"
      >
        ✏️
      </button>
      <button 
        @click="$emit('delete', dignitaire.id)"
        class="bg-red-100 hover:bg-red-200 text-red-700 p-1.5 rounded-full shadow"
      >
        🗑️
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  dignitaire: any
}>()

defineEmits(['edit', 'delete'])
</script>
```

#### 2. Pages (pages/)
```bash
touch pages/login.vue
touch pages/nominations/index.vue
touch pages/decorations/index.vue
touch pages/dignitaires/[id].vue
```

**Contenu de login.vue :**
```vue
<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">
        Connexion administrateur
      </h2>
      
      <div v-if="error" class="mb-4 text-red-600 text-center">
        {{ error }}
      </div>

      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Nom d'utilisateur
          </label>
          <input
            v-model="credentials.username"
            type="text"
            required
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Mot de passe
          </label>
          <input
            v-model="credentials.password"
            type="password"
            required
            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition disabled:opacity-50"
        >
          {{ loading ? 'Connexion...' : 'Se connecter' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: false
})

const authStore = useAuthStore()
const router = useRouter()

const credentials = reactive({
  username: '',
  password: ''
})

const error = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value = ''
  loading.value = true

  try {
    const success = await authStore.login(credentials)
    if (success) {
      router.push('/')
    } else {
      error.value = 'Identifiants invalides'
    }
  } catch (e) {
    error.value = 'Erreur de connexion'
  } finally {
    loading.value = false
  }
}
</script>
```

#### 3. Middleware (middleware/)
```bash
touch middleware/auth.ts
```

#### 4. Assets (assets/css/)
```bash
mkdir -p assets/css
touch assets/css/main.css
```

**Contenu de main.css :**
```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

#### 5. Configuration Tailwind
```bash
touch tailwind.config.js
```

**Contenu de tailwind.config.js :**
```js
module.exports = {
  content: [
    './components/**/*.{js,vue,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './plugins/**/*.{js,ts}',
    './app.vue',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

---

## 🚀 Commandes Rapides

### Créer tous les fichiers backend
```bash
cd backend
php artisan make:model Nomination
php artisan make:model Decoration
php artisan make:controller Api/AuthController
php artisan make:controller Api/NominationController --api
php artisan make:controller Api/DecorationController --api
php artisan make:controller Api/ReferentielController
```

### Créer tous les fichiers frontend
```bash
cd frontend
mkdir -p components pages/nominations pages/decorations middleware assets/css
```

---

## ✅ Checklist de Vérification

- [ ] Tous les models Laravel créés
- [ ] Tous les controllers API créés
- [ ] Routes API configurées
- [ ] Migrations exécutées
- [ ] Sanctum installé et configuré
- [ ] CORS configuré
- [ ] Tous les composants Vue créés
- [ ] Toutes les pages Nuxt créées
- [ ] Store Pinia configuré
- [ ] Middleware d'authentification créé
- [ ] Tailwind CSS configuré
- [ ] Variables d'environnement configurées
- [ ] Test de connexion réussi
- [ ] Test CRUD dignitaires réussi

---

## 📞 Prochaines Étapes

1. Exécuter `composer install` dans backend/
2. Exécuter `npm install` dans frontend/
3. Configurer les fichiers .env
4. Lancer les migrations
5. Créer un utilisateur de test
6. Tester la connexion
7. Implémenter les fonctionnalités restantes
