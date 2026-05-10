# Optimisation des performances

## Problèmes identifiés
1. **Déconnexions fréquentes** - L'utilisateur doit se reconnecter souvent
2. **Lenteur générale** - Les actions prennent plusieurs secondes
3. **Connexion lente** - Plusieurs secondes pour afficher le dashboard

## Causes probables

### 1. Token Sanctum expire trop vite
Par défaut, les tokens Sanctum expirent après un certain temps.

### 2. Trop de requêtes API
Chaque page fait plusieurs appels API séparés au lieu d'un seul appel optimisé.

### 3. Pas de cache
Les données de référence (pays, villes, entités) sont rechargées à chaque fois.

### 4. Relations non optimisées
Les relations sont chargées avec N+1 queries au lieu d'eager loading.

## Solutions à appliquer

### 1. Augmenter la durée de vie des tokens
```php
// backend/config/sanctum.php
'expiration' => 60 * 24 * 7, // 7 jours au lieu de 60 minutes
```

### 2. Créer un endpoint unique pour le dashboard
Au lieu de 3 appels séparés (stats, dignitaires, user), un seul appel.

### 3. Ajouter du cache côté frontend
Stocker les données de référence dans Pinia pour ne pas les recharger.

### 4. Optimiser les requêtes avec eager loading
Charger toutes les relations en une seule requête.

### 5. Ajouter un loading state
Afficher un indicateur de chargement pendant les requêtes.

### 6. Persister le token dans localStorage
Ne pas perdre la session au rafraîchissement de la page.
