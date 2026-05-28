# Guide des Migrations - Gestion des Dignitaires

## 📋 Vue d'ensemble

Ce dossier contient LA migration unique complète de la base de données du projet.

---

## 🎯 Migration Unique (SEULE ET UNIQUE)

### **`2027_01_01_000000_create_complete_schema.php`**

**Cette migration contient TOUTE la structure de la base de données en un seul fichier.**

#### Contenu COMPLET :
1. ✅ Tables Laravel (users, password_resets, failed_jobs, personal_access_tokens)
2. ✅ Tables de base (roles, fonctions, domaines, langues, régions, pays, villes)
3. ✅ Tables principales (dignitaires, structures, établissements, entités, PV, décorations)
4. ✅ Tables relationnelles (diplômes, enfants, langues parlées, expériences, postes, nominations)
5. ✅ Tables de contact (téléphones, emails)
6. ✅ Tables pivot (permissions et relations)
7. ✅ Index optimisés sur toutes les tables
8. ✅ Support région/province avec continent et pays

#### Utilisation :

**Pour un nouveau déploiement :**
```bash
php artisan migrate
```

**⚠️ IMPORTANT : Si votre base existe déjà, NE PAS exécuter cette migration !**
Cette migration est conçue pour créer une base de données complète from scratch.

---

## 📁 Anciennes Migrations

**TOUTES les anciennes migrations ont été supprimées et consolidées dans la migration unique.**

Anciennes migrations (maintenant supprimées) :
- ~~Laravel (4 migrations)~~
- ~~Projet initial (5 migrations)~~
- ~~Migration consolidée partielle (1 migration)~~

**Total : 10 migrations → 1 migration unique** ✅

---

## 🚀 Commandes Utiles

### Vérifier le statut
```bash
php artisan migrate:status
```

### Exécuter la migration (nouveau projet uniquement)
```bash
php artisan migrate
```

### Vérifier le schéma
```bash
php verify_schema.php
```

### Annuler la migration (⚠️ SUPPRIME TOUTES LES DONNÉES)
```bash
php artisan migrate:rollback
```

---

## 📝 Bonnes Pratiques

### Pour les nouveaux développeurs

1. **Cloner le projet**
2. **Configurer `.env`**
3. **Exécuter `php artisan migrate`**
4. **Vérifier avec `php verify_schema.php`**

### Pour les développeurs existants

**⚠️ NE PAS exécuter la migration si votre base existe déjà !**

Votre base de données actuelle contient déjà toutes les tables.
Cette migration est uniquement pour les nouveaux déploiements.

---

## ⚠️ Notes Importantes

1. **Migration unique = Simplicité**
   - Un seul fichier à gérer
   - Documentation complète en un endroit
   - Pas de dépendances entre migrations

2. **Sécurité**
   - Toujours faire un backup avant toute opération
   - Tester sur un environnement de développement d'abord

3. **Performance**
   - Tous les index sont créés automatiquement
   - Foreign keys optimisées
   - Structure complète en une seule exécution

---

**Dernière mise à jour :** 21 Mai 2026  
**Version :** 2.0 (Migration Unique)

