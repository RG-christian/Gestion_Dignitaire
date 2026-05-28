# Instructions pour Appliquer les Corrections

## Problème
Les modifications du middleware d'authentification ne sont pas appliquées car le serveur frontend n'a pas été redémarré.

## Solution : Redémarrer le Frontend

### Option 1 : Script PowerShell (Recommandé)
```powershell
# Depuis le dossier gestion-dignitaire-v2
.\restart-frontend.ps1
```

### Option 2 : Manuellement

#### Étape 1 : Arrêter le serveur actuel
1. Trouver le processus Node.js qui utilise le port 3000
2. L'arrêter (Ctrl+C dans le terminal ou via le gestionnaire de tâches)

#### Étape 2 : Redémarrer
```powershell
cd frontend
npm run dev
```

### Option 3 : Via le Gestionnaire de Tâches Windows
1. Ouvrir le Gestionnaire de tâches (Ctrl+Shift+Esc)
2. Chercher "Node.js" dans les processus
3. Terminer tous les processus Node.js
4. Redémarrer avec `npm run dev`

## Vérification

Après le redémarrage, testez :

### Test 1 : Rechargement de page
1. Connectez-vous à l'application
2. Allez sur `/dignitaires`
3. Appuyez sur F5 (recharger)
4. ✅ Vous devez **rester sur `/dignitaires`** (pas de redirection)

### Test 2 : Accès direct
1. Déconnectez-vous
2. Tapez `http://localhost:3000/postes` dans l'URL
3. ✅ Vous devez être redirigé vers `/login?redirect=/postes`
4. Connectez-vous
5. ✅ Vous devez arriver sur `/postes`

## Modifications Appliquées

### 1. Middleware Auth (`middleware/auth.ts`)
- ✅ Ne s'exécute que côté client (évite les problèmes SSR)
- ✅ Charge le token depuis localStorage avant toute vérification
- ✅ Sauvegarde l'URL d'origine dans `query.redirect`
- ✅ Nettoie le localStorage si le token est expiré

### 2. Page Login (`pages/login.vue`)
- ✅ Récupère le paramètre `redirect` depuis l'URL
- ✅ Redirige vers la page d'origine après connexion

## Pourquoi le Redémarrage est Nécessaire ?

Nuxt.js utilise un système de **Hot Module Replacement (HMR)** qui recharge automatiquement les composants Vue, mais :

❌ **Les middlewares ne sont PAS rechargés automatiquement**  
❌ **Les modifications du routing nécessitent un redémarrage**  
❌ **Les changements dans les stores peuvent nécessiter un redémarrage**

C'est pourquoi vous devez **toujours redémarrer** après avoir modifié :
- Les middlewares (`middleware/`)
- Les plugins (`plugins/`)
- La configuration Nuxt (`nuxt.config.ts`)
- Les stores Pinia (parfois)

## Cache du Navigateur

Si le problème persiste après le redémarrage, videz le cache du navigateur :

### Chrome/Edge
1. F12 (Outils de développement)
2. Clic droit sur le bouton de rechargement
3. "Vider le cache et effectuer une actualisation forcée"

### Firefox
1. Ctrl+Shift+Delete
2. Cocher "Cache"
3. Cliquer sur "Effacer maintenant"

## Dépannage

### Le serveur ne démarre pas
```powershell
# Vérifier si le port 3000 est occupé
netstat -ano | findstr :3000

# Tuer le processus (remplacer PID par le numéro)
taskkill /PID <PID> /F
```

### Erreur "Cannot find module"
```powershell
# Réinstaller les dépendances
cd frontend
rm -r node_modules
rm package-lock.json
npm install
```

### Le problème persiste
1. Vérifier que vous êtes sur la bonne branche Git
2. Vérifier que les fichiers ont bien été modifiés
3. Vider complètement le cache du navigateur
4. Redémarrer le navigateur
5. Vérifier la console du navigateur pour les erreurs

## Support

Si le problème persiste après toutes ces étapes, vérifiez :
- Les logs du serveur frontend (dans le terminal)
- La console du navigateur (F12)
- Les erreurs réseau (onglet Network dans F12)
