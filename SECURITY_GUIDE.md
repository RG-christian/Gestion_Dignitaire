# Guide de Sécurité - Gestion Dignitaire

## 📋 Checklist de sécurité pour les développeurs

### ✅ Pour chaque formulaire

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
    
    if (!$validator->isValid()) {
        $errors = $validator->getErrors();
        // Afficher les erreurs
    }
    
    // 3. Sanitizer les données
    $nom = Validator::sanitize($_POST['nom']);
    
    // 4. Traiter les données
}
```

### ✅ Pour chaque page protégée

```php
<?php
require_once 'config/security.php';
secureSession();
requireAuth(); // Redirige si non authentifié

// Votre code ici
?>
```

### ✅ Pour chaque upload de fichier

```php
require_once 'config/upload.php';

$uploader = new FileUploader(
    'uploads/photos/',
    ['jpg', 'jpeg', 'png'], // Extensions autorisées
    5242880 // 5MB max
);

$result = $uploader->upload($_FILES['photo']);

if ($result['success']) {
    $filename = $result['filename'];
    // Sauvegarder $filename en base
} else {
    $error = $result['error'];
}
```

### ✅ Pour chaque requête SQL

```php
// ✅ BON - Requête préparée
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

// ❌ MAUVAIS - Injection SQL possible
$sql = "SELECT * FROM users WHERE id = $id";
$result = $pdo->query($sql);
```

### ✅ Pour chaque affichage de données utilisateur

```php
// ✅ BON - Échappement HTML
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
// ou
echo e($userInput); // helper

// ❌ MAUVAIS - XSS possible
echo $userInput;
```

### ✅ Pour chaque action importante

```php
require_once 'config/logger.php';

getLogger()->info("Utilisateur créé", [
    'user_id' => $userId,
    'username' => $username,
    'ip' => getClientIp()
]);
```

## 🔒 Règles de sécurité strictes

### 1. Mots de passe

```php
// ✅ Hachage
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// ✅ Vérification
if (password_verify($inputPassword, $hashedPassword)) {
    // OK
}

// ❌ JAMAIS stocker en clair
// ❌ JAMAIS utiliser MD5 ou SHA1
```

### 2. Sessions

```php
// ✅ Toujours utiliser secureSession()
secureSession();

// ✅ Régénérer l'ID après login
session_regenerate_id(true);

// ✅ Détruire proprement
session_unset();
session_destroy();
```

### 3. Validation des entrées

```php
// ✅ Valider TOUTES les entrées
$validator = new Validator($_POST);
$validator->required('email')->email('email');
$validator->required('age')->in('age', [18, 25, 30, 35]);

// ✅ Whitelist plutôt que blacklist
$allowedValues = ['admin', 'user', 'guest'];
if (!in_array($role, $allowedValues)) {
    die('Rôle invalide');
}
```

### 4. Contrôle d'accès

```php
// ✅ Vérifier l'authentification
requireAuth();

// ✅ Vérifier les permissions
if (!hasRole('admin')) {
    die('Accès refusé');
}

// ✅ Vérifier la propriété des ressources
if ($resource->user_id !== $_SESSION['user_id']) {
    die('Accès refusé');
}
```

### 5. Gestion des erreurs

```php
// ✅ En production
try {
    // Code
} catch (Exception $e) {
    getLogger()->error($e->getMessage());
    die('Une erreur est survenue');
}

// ❌ Ne JAMAIS afficher les détails en production
// die($e->getMessage()); // Expose des infos sensibles
```

## 🚨 Vulnérabilités courantes à éviter

### SQL Injection
```php
// ❌ VULNÉRABLE
$sql = "SELECT * FROM users WHERE username = '$username'";

// ✅ SÉCURISÉ
$sql = "SELECT * FROM users WHERE username = ?";
$stmt->execute([$username]);
```

### XSS (Cross-Site Scripting)
```php
// ❌ VULNÉRABLE
echo "<div>$userComment</div>";

// ✅ SÉCURISÉ
echo "<div>" . e($userComment) . "</div>";
```

### CSRF (Cross-Site Request Forgery)
```php
// ❌ VULNÉRABLE
<form method="post">
    <input name="delete_user" value="123">
</form>

// ✅ SÉCURISÉ
<form method="post">
    <?= csrfField() ?>
    <input name="delete_user" value="123">
</form>
```

### Path Traversal
```php
// ❌ VULNÉRABLE
$file = $_GET['file'];
include("uploads/$file");

// ✅ SÉCURISÉ
$file = basename($_GET['file']); // Supprime ../
$allowedFiles = ['doc1.pdf', 'doc2.pdf'];
if (in_array($file, $allowedFiles)) {
    include("uploads/$file");
}
```

### Upload de fichiers malveillants
```php
// ❌ VULNÉRABLE
move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $_FILES['file']['name']);

// ✅ SÉCURISÉ
$uploader = new FileUploader();
$result = $uploader->upload($_FILES['file']);
```

## 📊 Audit de sécurité

### Commandes utiles

```bash
# Rechercher les echo non échappés
grep -r "echo \$" --include="*.php"

# Rechercher les requêtes SQL non préparées
grep -r "\$pdo->query" --include="*.php"

# Rechercher les include/require avec variables
grep -r "include.*\$" --include="*.php"

# Vérifier les permissions
find uploads/ -type f -exec ls -l {} \;
```

### Tests de sécurité

1. **Test CSRF** : Soumettre un formulaire sans token
2. **Test SQL Injection** : Entrer `' OR '1'='1` dans un champ
3. **Test XSS** : Entrer `<script>alert('XSS')</script>`
4. **Test Upload** : Uploader un fichier .php
5. **Test Session** : Vérifier le timeout après 30 minutes

## 🔐 Configuration serveur recommandée

### Apache (.htaccess)

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
Header set Referrer-Policy "strict-origin-when-cross-origin"
```

### PHP (php.ini)

```ini
; Désactiver les fonctions dangereuses
disable_functions = exec,passthru,shell_exec,system,proc_open,popen

; Limiter les uploads
upload_max_filesize = 5M
post_max_size = 6M

; Sessions sécurisées
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

## 📞 En cas d'incident de sécurité

1. **Isoler** : Mettre l'application hors ligne
2. **Analyser** : Consulter les logs (`logs/app.log`)
3. **Corriger** : Patcher la vulnérabilité
4. **Notifier** : Informer les utilisateurs si nécessaire
5. **Documenter** : Noter l'incident et les actions prises
