# 🔧 Installation Alternative - Sans Composer

## ⚠️ SI COMPOSER NE FONCTIONNE PAS

Voici une méthode alternative pour installer le projet sans utiliser Composer.

---

## 📦 MÉTHODE 1 : Téléchargement Direct

### Étape 1 : Télécharger Laravel

1. Allez sur : https://github.com/laravel/laravel/releases/tag/v10.0.0
2. Cliquez sur "Source code (zip)"
3. Téléchargez le fichier

### Étape 2 : Extraire

```powershell
cd C:\MAMP\htdocs\Gestion_Dignitaire\gestion-dignitaire-v2

# Supprimer l'ancien backend
Remove-Item -Recurse -Force backend

# Extraire le ZIP téléchargé
# Renommer le dossier extrait en "backend"
```

### Étape 3 : Installer les Dépendances

```powershell
cd backend

# Augmenter le timeout
composer config process-timeout 600

# Installer avec retry
composer install --no-interaction --prefer-dist
```

Si ça échoue encore :

```powershell
# Installer package par package
composer require laravel/framework:^10.0
composer require laravel/sanctum
composer require guzzlehttp/guzzle
```

---

## 🌐 MÉTHODE 2 : Utiliser un Miroir

### Configurer un Miroir Rapide

```powershell
# Miroir Aliyun (Chine - très rapide)
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# OU Miroir Tencent
composer config -g repo.packagist composer https://mirrors.cloud.tencent.com/composer/

# OU Miroir Huawei
composer config -g repo.packagist composer https://repo.huaweicloud.com/repository/php/

# Puis réessayer
composer create-project laravel/laravel backend "^10.0"
```

---

## 💾 MÉTHODE 3 : Utiliser l'Ancien Projet (PLUS SIMPLE)

Au lieu de migrer complètement, vous pouvez **ajouter l'API à votre projet existant** :

### Étape 1 : Copier les Fichiers dans l'Ancien Projet

```powershell
cd C:\MAMP\htdocs\Gestion_Dignitaire

# Copier les nouveaux Models
Copy-Item gestion-dignitaire-v2\backend\app\Models\* classes\ -Force

# Copier les nouveaux Controllers
New-Item -ItemType Directory -Force controllers\Api
Copy-Item gestion-dignitaire-v2\backend\app\Http\Controllers\Api\* controllers\Api\ -Force
```

### Étape 2 : Créer un Fichier API

Créez `api.php` dans votre ancien projet :

```php
<?php
// api.php - Point d'entrée API

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

// Router simple
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Routes API
if (preg_match('#^/api/login$#', $uri) && $method === 'POST') {
    require_once __DIR__ . '/controllers/Api/AuthController.php';
    $controller = new \App\Http\Controllers\Api\AuthController();
    echo json_encode($controller->login());
    exit;
}

if (preg_match('#^/api/dignitaires$#', $uri) && $method === 'GET') {
    require_once __DIR__ . '/controllers/Api/DignitaireController.php';
    $controller = new \App\Http\Controllers\Api\DignitaireController();
    echo json_encode($controller->index());
    exit;
}

// ... autres routes

http_response_code(404);
echo json_encode(['error' => 'Route not found']);
```

### Étape 3 : Configurer le Frontend

Dans `frontend/.env` :

```env
NUXT_PUBLIC_API_BASE=http://localhost/Gestion_Dignitaire/api.php
```

---

## 🎯 MÉTHODE 4 : Utiliser Docker (Avancé)

Si vous avez Docker installé :

```powershell
# Créer un Dockerfile
cd gestion-dignitaire-v2\backend
```

Créez `Dockerfile` :

```dockerfile
FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

Puis :

```powershell
docker build -t gestion-dignitaire-backend .
docker run -p 8000:8000 gestion-dignitaire-backend
```

---

## 🔄 MÉTHODE 5 : Réessayer avec Patience

Parfois, il suffit de réessayer plusieurs fois :

```powershell
# Script de retry automatique
$maxRetries = 5
$retryCount = 0

while ($retryCount -lt $maxRetries) {
    Write-Host "Tentative $($retryCount + 1)/$maxRetries..." -ForegroundColor Yellow
    
    composer create-project laravel/laravel backend "^10.0" --prefer-dist
    
    if ($?) {
        Write-Host "Installation reussie!" -ForegroundColor Green
        break
    }
    
    $retryCount++
    Start-Sleep -Seconds 10
}
```

---

## 🆘 SOLUTION ULTIME : Utiliser l'Ancien Projet

Si rien ne fonctionne, **gardez votre ancien projet PHP** et utilisez-le tel quel. Il fonctionne déjà !

La migration vers Laravel+Nuxt peut attendre que vous ayez :
- Une meilleure connexion internet
- Plus de temps
- Un environnement de développement plus stable

---

## 📞 RECOMMANDATION

**Pour l'instant, je vous recommande :**

1. **Gardez votre ancien projet PHP** qui fonctionne
2. **Utilisez le frontend Nuxt** avec l'ancien backend PHP (MÉTHODE 3)
3. **Migrez progressivement** quand vous aurez une meilleure connexion

Ou attendez que Composer finisse de télécharger (ça peut prendre 30-60 minutes avec une connexion lente).

---

## ✅ VÉRIFICATION

Pour vérifier si Composer fonctionne :

```powershell
# Tester la connexion
composer diagnose

# Vérifier la version
composer --version

# Tester un petit package
composer require psr/log
```

Si ces commandes fonctionnent, réessayez l'installation de Laravel.

---

**Quelle méthode voulez-vous essayer ?**
