# Démarrage manuel — Gestion Dignitaire

## Option A — environnement local

### 1. Démarrer MAMP

Dans MAMP, démarrer MySQL avec le port **3309**.

Vérifier que `backend/.env` contient :

```env
DB_HOST=127.0.0.1
DB_PORT=3309
```

### 2. Démarrer Mailpit local

Dans PowerShell :

```powershell
& "C:\tools\mailpit\mailpit.exe" --smtp 127.0.0.1:1027 --listen 127.0.0.1:8027
```

Interface : http://localhost:8027

### 3. Démarrer Laravel

Dans un deuxième terminal :

```powershell
cd "C:\Users\Georges RAPONTCHOMBO\MyWorkspace\projet-web-2026\Gestion_Dignitaire-1\gestion-dignitaire-v2\backend"
php artisan optimize:clear
php artisan serve --host=127.0.0.1 --port=8003
```

Backend : http://localhost:8003

### 4. Démarrer Nuxt

Dans un troisième terminal :

```powershell
cd "C:\Users\Georges RAPONTCHOMBO\MyWorkspace\projet-web-2026\Gestion_Dignitaire-1\gestion-dignitaire-v2\frontend"
npm run dev -- --port 3003
```

Frontend : http://localhost:3003

## Option B — environnement Docker

Dans PowerShell :

```powershell
cd "C:\Users\Georges RAPONTCHOMBO\MyWorkspace\projet-web-2026\Gestion_Dignitaire-1\gestion-dignitaire-v2"
docker compose up -d --build
docker compose ps
```

Accès Docker :

```text
Frontend : http://localhost:3002
Backend  : http://localhost:8000
Mailpit  : http://localhost:8025
MySQL    : localhost:3307
```

Initialisation Laravel après le premier démarrage :

```powershell
docker compose exec backend php artisan key:generate --force
docker compose exec backend php artisan migrate --force
docker compose exec backend php artisan storage:link
```

Arrêt Docker :

```powershell
docker compose down
```

## Vérification rapide

```powershell
Test-NetConnection 127.0.0.1 -Port 3003
Test-NetConnection 127.0.0.1 -Port 8003
Test-NetConnection 127.0.0.1 -Port 8027
```
## demamrer avec cette commande plus rapidement 

```powershell
cd "C:\Users\Georges RAPONTCHOMBO\MyWorkspace\projet-web-2026\Gestion_Dignitaire-1\gestion-dignitaire-v2"

powershell -ExecutionPolicy Bypass -File .\demarrer-projet.ps1 -Mode local
```
## voir les taches

```powershell
Get-Job GestionDignitaire*
```

## arrêter les services locaux

```powershell
powershell -ExecutionPolicy Bypass -File .\ARRET-PROJET.ps1 -Mode local
```

## Pour vérifier

```powershell
Get-Job GestionDignitaire*
Get-Process php,node,mailpit -ErrorAction SilentlyContinue
```


## ou encore plus rapide ouvrir le projet dnas le bon dossier 
C:\Users\Georges RAPONTCHOMBO\MyWorkspace\projet-web-2026\Gestion_Dignitaire-1\gestion-dignitaire-v2

## ensuite faire Ctrl + Shift + P
## ensuite choisir Tasks: Run Task
## voir 
LOCAL - Tout démarrer
Docker - Tout démarrer
Local - Backend Laravel
Local - Frontend Nuxt
Local - Mailpit
## et clique sur "LOCAL - Tout démarrer" 