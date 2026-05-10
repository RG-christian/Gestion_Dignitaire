# Backend Laravel - Gestion des Dignitaires

## 🚀 Installation

```bash
# Installer les dépendances
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données dans .env
# DB_DATABASE=gestion_dignitaire
# DB_USERNAME=root
# DB_PASSWORD=

# Exécuter les migrations
php artisan migrate

# Exécuter les seeders
php artisan db:seed

# Démarrer le serveur
php artisan serve
```

## 📚 API Endpoints

### Authentification
- POST `/api/login` - Connexion
- POST `/api/logout` - Déconnexion
- GET `/api/user` - Utilisateur connecté

### Dignitaires
- GET `/api/dignitaires` - Liste des dignitaires
- GET `/api/dignitaires/{id}` - Détails d'un dignitaire
- POST `/api/dignitaires` - Créer un dignitaire
- PUT `/api/dignitaires/{id}` - Modifier un dignitaire
- DELETE `/api/dignitaires/{id}` - Supprimer un dignitaire

### Nominations
- GET `/api/nominations` - Liste des nominations
- POST `/api/nominations` - Créer une nomination
- PUT `/api/nominations/{id}` - Modifier une nomination
- DELETE `/api/nominations/{id}` - Supprimer une nomination

### Décorations
- GET `/api/decorations` - Liste des décorations
- POST `/api/decorations` - Créer une décoration

### Référentiels
- GET `/api/pays` - Liste des pays
- GET `/api/villes` - Liste des villes
- GET `/api/entites` - Liste des entités
- GET `/api/postes` - Liste des postes

## 🔐 Authentification

L'API utilise Laravel Sanctum pour l'authentification par tokens.
