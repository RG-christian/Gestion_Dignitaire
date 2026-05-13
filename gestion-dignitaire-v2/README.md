# Gestion des Dignitaires

## Configuration

### Backend (Laravel)
- Port: 8000
- Base de données: MySQL (gestion_dignitaire)
- Authentification: Laravel Sanctum avec tokens API
- Durée de session: 7 jours

### Frontend (Nuxt 3)
- Port: 3000
- API: http://localhost:8000/api

## Démarrage

### Backend
```bash
cd backend
php artisan serve
```

### Frontend
```bash
cd frontend
npm run dev
```

## Authentification
Les tokens sont valides pendant 7 jours. Le système garde automatiquement votre session active.
