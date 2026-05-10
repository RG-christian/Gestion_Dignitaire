# 🚀 Guide d'Installation Complet - Gestion Dignitaires v2

## Architecture du Projet

```
gestion-dignitaire-v2/
├── backend/          # API Laravel
└── frontend/         # Application Nuxt.js
```

---

## 📦 Prérequis

- **PHP** >= 8.1
- **Composer** >= 2.0
- **Node.js** >= 18.0
- **MySQL** >= 8.0
- **Git**

---

## 🔧 Installation Backend (Laravel)

### 1. Initialiser le projet Laravel

```bash
cd gestion-dignitaire-v2/backend

# Installer Laravel (si pas déjà fait)
composer create-project laravel/laravel . "^10.0"

# Ou installer les dépendances
composer install
```

### 2. Configuration de l'environnement

```bash
# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate
```

### 3. Configurer la base de données

Éditer le fichier `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_dignitaire_v2
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### 4. Créer la base de données

```bash
# Via MySQL CLI
mysql -u root -p
CREATE DATABASE gestion_dignitaire_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 5. Installer Laravel Sanctum

```bash
php artisan install:api
```

### 6. Exécuter les migrations

```bash
php artisan migrate
```

### 7. Créer les seeders (optionnel)

```bash
php artisan db:seed
```

### 8. Lancer le serveur

```bash
php artisan serve
# API disponible sur http://localhost:8000
```

---

## 🎨 Installation Frontend (Nuxt.js)

### 1. Installer les dépendances

```bash
cd gestion-dignitaire-v2/frontend

npm install
# ou
yarn install
```

### 2. Configuration de l'environnement

```bash
cp .env.example .env
```

Éditer `.env` :

```env
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
```

### 3. Lancer le serveur de développement

```bash
npm run dev
# Application disponible sur http://localhost:3000
```

---

## 🗂️ Structure des Fichiers à Créer

### Backend Laravel

Créez ces fichiers dans `backend/app/` :

#### Models (app/Models/)
- ✅ `Dignitaire.php` (déjà créé)
- `Nomination.php`
- `Decoration.php`
- `Ville.php`
- `Pays.php`
- `Entite.php`
- `Poste.php`
- `Diplome.php`
- `Enfant.php`
- `Experience.php`
- `LangueParlee.php`
- `User.php` (modifier le modèle existant)

#### Controllers (app/Http/Controllers/Api/)
- ✅ `DignitaireController.php` (déjà créé)
- `NominationController.php`
- `DecorationController.php`
- `ReferentielController.php`
- `AuthController.php`
- `PosteController.php`

#### Exemple de AuthController.php

```php
<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user
            ]);
        }

        return response()->json(['message' => 'Identifiants invalides'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnexion réussie']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
```

### Frontend Nuxt

Créez ces fichiers dans `frontend/` :

#### Composants (components/)
- `DashboardLayout.vue`
- `DignitaireCard.vue`
- `DignitaireModal.vue`
- `StatCard.vue`
- `Pagination.vue`

#### Pages (pages/)
- ✅ `index.vue` (déjà créé)
- ✅ `dignitaires/index.vue` (déjà créé)
- `dignitaires/[id].vue`
- `nominations/index.vue`
- `decorations/index.vue`
- `login.vue`

#### Middleware (middleware/)
- `auth.ts`

#### Exemple de auth.ts

```typescript
export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore()
  
  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }
})
```

#### Exemple de DashboardLayout.vue

```vue
<template>
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-green-700 text-white fixed h-full">
      <div class="p-6">
        <h2 class="text-2xl font-bold">Gestion Dignitaires</h2>
      </div>
      <nav class="mt-6">
        <NuxtLink to="/" class="block px-6 py-3 hover:bg-green-600">
          📊 Dashboard
        </NuxtLink>
        <NuxtLink to="/dignitaires" class="block px-6 py-3 hover:bg-green-600">
          👥 Dignitaires
        </NuxtLink>
        <NuxtLink to="/nominations" class="block px-6 py-3 hover:bg-green-600">
          📋 Nominations
        </NuxtLink>
        <NuxtLink to="/decorations" class="block px-6 py-3 hover:bg-green-600">
          🏅 Décorations
        </NuxtLink>
        <button @click="logout" class="block w-full text-left px-6 py-3 hover:bg-green-600">
          🚪 Déconnexion
        </button>
      </nav>
    </aside>

    <!-- Main content -->
    <main class="ml-64 flex-1 p-8">
      <slot />
    </main>
  </div>
</template>

<script setup lang="ts">
const authStore = useAuthStore()

function logout() {
  authStore.logout()
}
</script>
```

---

## 🧪 Tester l'Installation

### 1. Tester l'API

```bash
# Créer un utilisateur de test
php artisan tinker
>>> $user = new App\Models\User();
>>> $user->username = 'admin';
>>> $user->nom_complet = 'Administrateur';
>>> $user->email = 'admin@example.com';
>>> $user->password = bcrypt('password');
>>> $user->role_id = 1;
>>> $user->save();
```

### 2. Tester la connexion

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password"}'
```

### 3. Accéder à l'application

Ouvrez http://localhost:3000 dans votre navigateur

---

## 📝 Prochaines Étapes

1. ✅ Créer tous les models manquants
2. ✅ Créer tous les controllers API
3. ✅ Créer tous les composants Vue
4. ✅ Implémenter l'upload de fichiers
5. ✅ Ajouter les tests unitaires
6. ✅ Déployer en production

---

## 🐛 Dépannage

### Erreur de connexion à la base de données
```bash
php artisan config:clear
php artisan cache:clear
```

### Erreur CORS
Ajouter dans `config/cors.php` :
```php
'paths' => ['api/*'],
'allowed_origins' => ['http://localhost:3000'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
'supports_credentials' => true,
```

### Erreur Sanctum
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

---

## 📞 Support

Pour toute question, consultez la documentation ou contactez l'équipe de développement.
