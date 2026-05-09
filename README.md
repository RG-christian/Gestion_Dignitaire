# Gestion Dignitaire

Application PHP de gestion des dignitaires (hauts fonctionnaires) avec système d'administration basé sur des rôles.

## 🚀 Installation

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Composer (optionnel mais recommandé)

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone <url-du-repo>
cd gestion_dignitaire
```

2. **Configurer l'environnement**
```bash
cp .env.example .env
```
Éditer le fichier `.env` avec vos paramètres de base de données.

3. **Installer les dépendances (optionnel)**
```bash
composer install
```

4. **Créer la base de données**
```sql
CREATE DATABASE gestion_dignitaire CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

5. **Exécuter les migrations**
```bash
php run_migrations.php
```

6. **Configurer les permissions**
```bash
chmod 755 uploads/
chmod 755 logs/
```

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

### Configuration de production

Pour un environnement de production, modifier dans `.env` :
```
APP_ENV=production
```

Et dans `php.ini` :
```ini
display_errors = Off
log_errors = On
error_log = /path/to/logs/php-error.log
```

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

## 🛠️ Utilisation

### Connexion
Accéder à `index.php` et se connecter avec les identifiants administrateur.

### Ajouter un token CSRF dans un formulaire
```php
<?php require_once 'config/security.php'; ?>
<form method="post">
    <?= csrfField() ?>
    <!-- Vos champs -->
</form>
```

### Valider des données
```php
require_once 'config/validator.php';

$validator = new Validator($_POST);
$validator->required('nom')
          ->minLength('nom', 3)
          ->email('email');

if ($validator->isValid()) {
    // Traiter les données
} else {
    $errors = $validator->getErrors();
}
```

### Logger une action
```php
require_once 'config/logger.php';

getLogger()->info("Action effectuée", ['user_id' => 123]);
getLogger()->error("Erreur survenue", ['details' => $error]);
```

### Uploader un fichier
```php
require_once 'config/upload.php';

$uploader = new FileUploader('uploads/photos/');
$result = $uploader->upload($_FILES['photo']);

if ($result['success']) {
    $filename = $result['filename'];
} else {
    $error = $result['error'];
}
```

## 🔧 Maintenance

### Logs
Les logs sont stockés dans `logs/app.log`. Surveiller régulièrement ce fichier.

### Sauvegardes
Sauvegarder régulièrement :
- La base de données MySQL
- Le dossier `uploads/`
- Le fichier `.env`

### Mises à jour
```bash
git pull
composer install
php run_migrations.php
```

## 📝 Développement

### Créer un nouveau contrôleur
1. Créer le fichier dans `controllers/`
2. Ajouter le nom dans la whitelist du routeur (`routers/Router.class.php`)

### Créer une nouvelle migration
```php
<?php
require_once __DIR__ . '/../config/database.php';
$pdo = getDatabaseConnection();
// Votre SQL ici
```

## 🐛 Dépannage

### Erreur de connexion à la base de données
Vérifier les paramètres dans `.env`

### Erreur "Token CSRF invalide"
Vérifier que les sessions sont bien activées et que les cookies sont autorisés

### Erreur d'upload de fichier
Vérifier les permissions du dossier `uploads/` (755)

## 📄 Licence

Propriétaire - Tous droits réservés
