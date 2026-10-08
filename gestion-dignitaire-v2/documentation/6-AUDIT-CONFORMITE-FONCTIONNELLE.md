# Audit de conformité fonctionnelle du projet Gestion Dignitaire

Date de l'audit : 2 octobre 2026

## 1. Conclusion exécutive

Le projet couvre une part importante du besoin métier décrit dans les trois documents de référence. L'architecture Laravel et Nuxt contient les modules structurants : authentification, permissions, dignitaires, candidatures, famille, diplômes, expériences, nominations, affectations, géographie, structures, documents, audit, exports, rapports et notifications par email.

Le projet n'est toutefois pas conforme de bout en bout à toutes les exigences. La compilation du frontend réussit, les routes Laravel sont chargées et, après le démarrage de MAMP, la base MySQL configurée sur `127.0.0.1:3307` répond correctement. Les endpoints publics principaux ont été validés, mais les parcours authentifiés avec écriture restent à exécuter avec une base de recette et des comptes couvrant chaque rôle.

Les écarts les plus importants sont :

1. absence de tests métier automatisés ;
2. impossibilité de valider les parcours connectés tant que MySQL est arrêté ;
3. gestion des procès-verbaux désormais implémentée de bout en bout dans le code et couverte par 19 assertions backend ; la validation visuelle dans le navigateur reste à effectuer ;
4. justificatif PDF des expériences administratives implémenté et couvert par 26 assertions ;
5. attestation de décoration désormais rattachée à l'attribution et stockée de manière privée ;
6. recherche globale textuelle, mais pas réellement multicritère ;
7. tableau de bord incomplet par rapport aux indicateurs demandés ;
8. archivage réversible désormais appliqué aux six ressources historiques centrales ; les ressources secondaires restent à classifier selon leur politique de conservation ;
9. absence d'écran de sessions actives et de journal de connexions spécialisé ;
10. absence de preuve que le planificateur Laravel tourne réellement en exploitation.

Appréciation globale : **conformité statique partielle à avancée** et **socle public opérationnel**, mais conformité opérationnelle complète non certifiée avant l'exécution des scénarios métier authentifiés de bout en bout.

## 2. Sources auditées

- `documentation/compte rendu de la reuinion Gestion dignitaire.docx`
- `documentation/compte rendu de la reuinion Gestion dignitaire.pdf`
- `documentation/fonctionalité Gestion dignitaire.docx`

Le PDF reprend le même compte rendu que le Word, sans exigence supplémentaire identifiable. Le contenu du compte rendu est dupliqué intégralement dans les deux formats.

## 3. Méthode et limites

Chaque exigence a été recherchée à travers les couches suivantes :

- page ou composant Nuxt ;
- route API Laravel ;
- contrôleur et validation serveur ;
- modèle et migration ;
- permission ;
- journalisation ;
- test automatisé ou vérification exécutable.

Statuts utilisés :

- **Conforme** : chaîne principale présente et cohérente dans le code ;
- **Partiel** : fonction utilisable en partie ou exigence incomplète ;
- **Absent** : aucune chaîne utilisable de bout en bout ;
- **Non vérifiable en exécution** : code présent, mais dépendance d'exécution indisponible.

Limite principale : les contrôles authentifiés avec écriture n'ont pas été exécutés, afin de ne pas modifier la base métier existante. Aucun ajout, aucune modification et aucune suppression de donnée métier n'a été effectué pendant cet audit.

## 4. Matrice de conformité par module

### 4.1 Authentification et administration

Statut : **Partiel avancé**

Implémenté :

- connexion administrateur et candidat avec Sanctum ;
- mots de passe hashés ;
- oubli et réinitialisation de mot de passe ;
- OTP à six chiffres, activable séparément pour administrateurs et candidats ;
- création, modification et suppression d'utilisateurs par super administrateur ;
- rôles et sous-fonctions avec niveaux lecture et écriture ;
- interdiction de suppression pour Assistant et Gestionnaire ;
- éviction de session et audit des connexions ;
- écrans de connexion, OTP, profil et paramètres.

Écarts :

- la désactivation réversible d'un compte n'est pas clairement séparée de sa suppression ;
- aucun écran ne liste les sessions actives ;
- le journal des connexions n'est pas présenté comme un module spécialisé avec succès, échec, IP et appareil ;
- les deux documents ne donnent pas exactement les mêmes rôles : le compte rendu demande Assistant, Gestionnaire, Administrateur et Super Administrateur, tandis que le document fonctionnel cite aussi Consultant et Observateur. Le code suit la première nomenclature.

Solution : ajouter `actif`, `desactive_le` et `desactive_par` sur les utilisateurs, refuser l'authentification d'un compte inactif, créer une page de sessions et faire arbitrer officiellement la nomenclature des rôles.

### 4.2 Gestion des dignitaires

Statut : **Partiel avancé**

Implémenté :

- CRUD, photo, fiche détaillée et matricule automatique ;
- NIP, identité, naissance, genre, état civil, nationalité, adresses et coordonnées ;
- filtres, statuts actif, retraité et non localisé ;
- vues liste et carte ;
- import Excel, export Excel, export PDF et fiche PDF ;
- relations vers postes, nominations, diplômes, langues, décorations, expériences, enfants, conjoints, documents et affectations ;
- historique d'audit affiché sur la fiche lorsque l'utilisateur y est autorisé.

Écarts :

- l'historique complet dépend du fait que chaque contrôleur appelle manuellement `AuditLogger` ;
- un échec de journalisation est ignoré et n'annule pas l'action métier ;
- l'ancienneté n'est pas exposée comme un indicateur métier unifié et clairement calculé à partir de la date retenue par le métier ;
- les suppressions restent physiques sur plusieurs ressources.

Solution : centraliser l'audit dans des observers ou événements transactionnels, définir la règle officielle d'ancienneté, ajouter un champ/calcul documenté et activer les suppressions logiques pour les données sensibles.

### 4.3 Famille et état matrimonial

Statut : **Avancé — chronologie familiale validée techniquement le 5 octobre 2026**

Implémenté :

- CRUD des enfants ;
- CRUD des conjoints ;
- statut d'union, date de début, fin d'union et motif ;
- informations de naissance, sexe et relation au dignitaire ;
- consultation depuis la fiche du dignitaire et pages dédiées ;
- chronologie familiale en lecture seule regroupant naissances, unions et fins d'union ;
- conservation et test de l'union initiale après sa clôture.

Écarts :

- pas d'arbre familial visuel.

Solution restante : ajouter un arbre familial uniquement si le métier confirme ce besoin distinct de la chronologie désormais disponible.

### 4.4 Éducation et formations

Statut : **Avancé — chronologie et référentiels académiques validés techniquement le 5 octobre 2026**

Implémenté :

- CRUD des diplômes ;
- recherche par intitulé, établissement et année ;
- filtre par dignitaire ;
- établissement, ville, domaine, année, code et type ;
- justificatif PDF limité à 10 Mo ;
- création rapide d'un établissement ;
- export PDF et Excel ;
- chronologie académique triée par année et regroupée par niveau ;
- pages dédiées de gestion des Domaines et Établissements ;
- CRUD complet, prévention des doublons de nom et blocage de suppression lorsqu'un diplôme utilise encore la valeur ;
- établissement enrichi par son type et sa ville.

Écarts :

- aucune route explicite de téléchargement ou prévisualisation du justificatif de diplôme ;

Solution restante : ajouter une route de téléchargement autorisée pour le justificatif de diplôme afin d'homogénéiser sa protection avec les justificatifs d'expérience.

### 4.5 Expériences professionnelles

Statut : **Avancé — justificatif PDF et chronologie complétés les 4 et 5 octobre 2026**

Implémenté :

- CRUD des expériences ;
- poste/intitulé, structure, dates de début et de fin ;
- recherche et filtre par dignitaire ;
- copie des expériences lors de la validation d'une candidature ;
- ajout et remplacement d'un justificatif PDF limité à 10 Mo dans le CRUD administratif ;
- téléchargement par une route authentifiée soumise à la permission `Expérience` ;
- suppression du fichier remplacé ou associé à une expérience supprimée ;
- tests transactionnels de validation du type, upload, remplacement, téléchargement et suppression ;
- timeline de carrière triée par date de début décroissante ;
- expérience sans date de fin explicitement signalée comme `En cours`.

Écart restant : la chronologie ne fusionne pas encore les expériences avec les postes, nominations et affectations dans une unique carrière institutionnelle.

Solution restante : confirmer avec le métier si ces quatre sources doivent être fusionnées ou rester présentées dans leurs modules respectifs.

### 4.6 Nominations, postes et carrière politique

Statut : **Partiel avancé — gestion des PV complétée le 2 octobre 2026**

Implémenté :

- CRUD des nominations et des postes ;
- plusieurs nominations par dignitaire ;
- dates, statut en cours/terminé et clôture manuelle ;
- motif et type de fin permettant de distinguer une fin formelle d'une remise à disposition ;
- affectations séparées de la nationalité ;
- email lors d'une nomination ;
- rappel quotidien avant expiration d'un mandat ;
- export PDF et Excel ;
- journalisation des créations, modifications, clôtures et suppressions.

Écarts :

- pas de workflow distinct brouillon, soumis, validé et rejeté pour une nomination ;
- la gestion des procès-verbaux est désormais complète : table enrichie, modèle corrigé sur la table historique `pv`, CRUD API, PDF, permission dédiée, audit, archivage/restauration, écran `/pvs` et association depuis une nomination ;
- la conservation historique est fragilisée par la route de suppression physique ;
- le fonctionnement des rappels dépend d'un planificateur système non vérifié pendant l'audit.

Reste à faire : interdire la suppression physique des nominations historisées, ajouter un workflow de validation si confirmé par le métier et superviser `schedule:run` ou `schedule:work` en production.

### 4.7 Langues

Statut : **Avancé — support de la famille et niveaux linguistiques validé techniquement le 5 octobre 2026**

Implémenté :

- référentiel des langues ;
- association langue-dignitaire ;
- niveau de maîtrise ;
- gestion depuis le candidat et depuis l'administration ;
- champ optionnel `famille`, affiché, modifiable et interrogeable dans la recherche ;
- niveaux administratifs limités aux quatre valeurs métier : Débutant, Moyen, Courant et Bilingue ;
- statistique de répartition par langue dans le tableau de bord.

Écarts :

- le compte rendu indique seulement qu'un classement par famille ou origine linguistique a été « évoqué » ; il ne définit ni taxonomie, ni différence entre famille et origine, ni règle d'affectation ;
- les sept langues présentes ont actuellement une famille vide.

Solution restante : faire valider par le métier la taxonomie officielle et décider si `famille` et `origine linguistique` désignent une seule notion. Le support logiciel est opérationnel, mais aucune valeur ne doit être inventée avant cette décision.

### 4.8 Distinctions et décorations

Statut : **Partiel avancé — attribution et attestation sécurisées le 4 octobre 2026**

Implémenté :

- CRUD du référentiel de décorations ;
- nom, type, niveau, grade, date, autorité, motif et description ;
- association à un dignitaire ;
- recherche et exports PDF/Excel.
- ressource d'attribution dédiée avec date et poste occupé ;
- validation que le poste appartient au dignitaire sélectionné ;
- upload PDF limité à 10 Mo dans le stockage privé ;
- nom original, type MIME, taille et empreinte SHA-256 ;
- remplacement, téléchargement authentifié et nettoyage du fichier ;
- conservation des deux anciennes références textuelles sans créer de faux fichiers ;
- protection contre la suppression d'une décoration encore attribuée.

Écarts restants :

- les listes déroulantes contrôlées pour type, niveau et grade ne reposent pas sur des référentiels serveur garantis.

Solution restante : créer des référentiels serveur contrôlés pour les types, niveaux et grades.

### 4.9 Géographie

Statut : **Conforme sur le socle vérifié**

Implémenté :

- CRUD des pays, régions et villes ;
- relations hiérarchiques utilisées dans les modèles et formulaires ;
- filtres et recherche ;
- référentiels publics limités pour l'inscription ;
- ville de naissance, pays et localisation des structures.

Écarts :

- l'utilisation actuelle d'une API mondiale externe n'a pas été retrouvée comme intégration active ;
- la base contient 40 pays, 206 régions et 586 villes ; aucune ville sans pays et aucun pays sans région n'ont été détectés ;
- la cohérence sémantique complète des rattachements et des continents reste à contrôler avec le métier.

Solution : ajouter un test d'intégrité géographique, documenter la source d'import et prévoir une commande d'import idempotente plutôt qu'une dépendance directe permanente à une API externe.

### 4.10 Organisations, structures et entités

Statut : **Partiel avancé**

Implémenté :

- CRUD des structures ;
- CRUD des entités ;
- type, entité parente et données de rattachement ;
- utilisation dans les postes, expériences et nominations ;
- exports des structures et entités.

Écarts :

- la distinction fonctionnelle entre `structure` et `entite` reste peu documentée ;
- pas de prévention démontrée des cycles dans la hiérarchie d'entités ;
- pas de tests de suppression d'une entité ou structure déjà référencée.

Solution : formaliser les deux concepts, ajouter une validation anti-cycle et des contraintes de suppression avec messages métier.

### 4.11 Tableau de bord

Statut : **Conforme techniquement sur les indicateurs définis le 7 octobre 2026**

Implémenté :

- nombres de dignitaires, postes, décorations, villes, pays, régions et diplômes ;
- nombres d'actifs, retraités et non localisés ;
- répartition hommes/femmes, régions, postes et statuts ;
- nominations et candidatures par mois ;
- derniers dignitaires et activité récente ;
- total dédié aux nominations et total des dignitaires déclarés militaires ;
- répartitions par pays d'affectation, domaine, langue et niveau académique ;
- derniers utilisateurs pour les administrateurs, dernières nominations et dernières décorations ;
- statut militaire explicite et grade sur la fiche dignitaire ;
- exclusion des archives dans les totaux et répartitions historiques concernés.

Écarts :

- la notion de récurrence des dignitaires n'est pas définie ni calculée.

Solution restante : faire définir par le métier la notion de récurrence avant d'ajouter un indicateur qui risquerait sinon d'être trompeur.

### 4.12 Recherche globale

Statut : **Avancé — recherche multicritère validée techniquement le 5 octobre 2026**

Implémenté :

- recherche textuelle unique ;
- dignitaires, candidats, nominations, postes, diplômes, décorations et entités ;
- filtrage des catégories de résultats selon les permissions ;
- liens vers les pages cibles ;
- écran avancé `/recherche` avec combinaison en ET des critères texte, poste, pays d'affectation, langue, domaine, niveau académique, structure, mandat actif et statut militaire ;
- listes de critères alimentées par les référentiels actifs ;
- résultats paginés avec choix du nombre de lignes ;
- accès backend protégé par la permission Dignitaire et exclusion des ressources archivées.

Écarts :

- la recherche rapide conserve sa limite de cinq résultats par type ;
- certaines ressources demandées, notamment expériences, affectations et documents, ne sont pas interrogées ;
- pas de classement par pertinence ni de recherche tolérante aux variantes.

Solution restante : conserver l'écran multicritère comme recherche structurée et compléter séparément la recherche rapide par les expériences, affectations et documents ; définir ensuite des critères de pertinence et de tolérance aux variantes avant toute modification du classement.

### 4.13 Archivage et historique

Statut : **Avancé — noyau historique et audit HTTP centralisés le 4 octobre 2026**

Implémenté :

- journal générique avec acteur, action, ressource, ancienne valeur, nouvelle valeur et date ;
- historique affichable par ressource ;
- conservation possible des nominations clôturées ;
- rapports PDF périodiques archivés ;
- suppression logique et restauration des dignitaires, nominations, postes, affectations, conjoints et attributions de décoration ;
- conservation des documents privés et des relations historiques lors de l'archivage ;
- inventaire central des éléments archivés dans `/admin/archives`, avec restauration par ressource.
- journalisation automatique de toute requête API d'écriture réussie, même lorsqu'un contrôleur n'appelle pas explicitement le journal ;
- regroupement par identifiant de requête, méthode HTTP, chemin, statut de réponse, adresse IP et agent utilisateur ;
- transaction englobante : une réponse d'écriture en erreur ou un échec du journal annule les écritures en base ;
- conservation des traces détaillées existantes sans création d'un doublon automatique.

Écarts :

- les suppressions des ressources secondaires (diplômes, expériences, enfants, documents candidat et certains référentiels) restent physiques et doivent être classifiées selon une politique métier explicite ;
- les commandes planifiées et autres traitements hors HTTP ne passent pas encore par la couche automatique ;
- la trace automatique garantit l'existence et le contexte de l'action, mais les anciennes et nouvelles valeurs détaillées restent fournies par les contrôleurs métier instrumentés ;
- pas encore de politique formellement validée pour la durée de rétention, le gel et le versement aux archives.

Solution restante : classifier les ressources secondaires, étendre le contexte d'audit aux traitements planifiés et faire valider la politique de conservation.

### 4.14 Gestion documentaire

Statut : **Partiel avancé — transfert candidat sécurisé le 4 octobre 2026**

Implémenté :

- documents des dignitaires avec type, nom, numéro, dates, organisme et description ;
- upload de PDF et images avec limite de taille ;
- téléchargement autorisé par authentification et permission Dignitaire ;
- détection des documents expirés ou proches de l'expiration ;
- documents, diplômes et expériences dans l'espace candidat ;
- dossiers de stockage spécialisés ;
- liaison automatique des documents candidat validés lors de la conversion en dignitaire, sans duplication physique ;
- traçabilité de la pièce source par `source_candidat_document_id` ;
- exclusion explicite des pièces en attente ou rejetées ;
- conservation du fichier partagé et de la photo si l'ancien dossier candidat est supprimé.

Écarts :

- pas de route de prévisualisation dédiée ;
- classement annuel surtout déduit des métadonnées, sans arborescence documentaire métier explicite ;
- stockage sur disque `public`, moins adapté aux pièces sensibles telles que passeport, casier judiciaire et certificat médical ;
- antivirus, chiffrement, empreinte du fichier et politique de conservation non visibles ;
- le justificatif d'expérience et l'attestation de décoration sont maintenant couverts, mais la sécurisation privée n'est pas encore homogène pour tous les autres types documentaires.

Solution : passer les pièces sensibles sur un disque privé, fournir une réponse contrôlée en streaming, ajouter empreinte SHA-256, analyse antivirus, règles de rétention et un composant documentaire partagé.

### 4.15 Sécurité avancée

Statut : **Partiel avancé**

Implémenté :

- Sanctum ;
- hash des mots de passe et des OTP ;
- expiration et limitation des tentatives OTP ;
- OTP activable, donc non imposé tant qu'il n'est pas activé ;
- permissions fines lecture/écriture/suppression ;
- audit des actions ;
- contrôle des types et tailles sur plusieurs uploads ;
- éviction de session précédente.

Écarts :

- pas de tableau des sessions actives ;
- pas de journal explicite des échecs de connexion ;
- l'adresse IP et l'agent utilisateur ne figurent pas dans la structure principale d'audit examinée ;
- stockage public de documents sensibles ;
- absence de tests d'autorisation par rôle et par méthode HTTP.

Solution : journaliser succès et échecs avec IP et agent utilisateur, créer une gestion de sessions, sécuriser le stockage et écrire une matrice de tests 401/403/200/201/204 pour chaque rôle.

### 4.16 Notifications et rapports

Statut : **Avancé — périodicité semestrielle validée techniquement le 5 octobre 2026**

Implémenté :

- emails d'OTP et de réinitialisation ;
- emails de création, validation et refus de candidature ;
- messages et notifications administratives liés aux pièces candidat ;
- email de nomination ;
- commande quotidienne de rappel d'expiration des mandats ;
- génération et archivage de rapports mensuels, trimestriels, semestriels et annuels ;
- envoi des rapports aux administrateurs ;
- page de consultation et téléchargement des rapports ;
- génération semestrielle planifiée les 1er janvier et 1er juillet sur le dernier semestre entièrement terminé ;
- filtre semestriel dans les archives ;
- absence de comptage artificiel des diplômes sur les périodes infra-annuelles, car leur date disponible est limitée à l'année.

Écarts :

- pas de preuve que le planificateur Laravel est lancé par le système ;
- pas de notifications temps réel ;
- pas de suivi de livraison des emails, de reprise automatique ni de file de messages démontrée ;

Solution restante : superviser le scheduler, utiliser une queue avec reprises, enregistrer le statut d'envoi et introduire du temps réel seulement si le besoin est confirmé.

## 5. Priorités spécifiques décidées en réunion

### Candidature avant dignitaire

Statut : **Conforme techniquement — conversion documentaire validée le 4 octobre 2026**

Le candidat possède un espace séparé avec inscription, OTP, profil, documents, diplômes, langues et expériences. L'administrateur peut examiner, valider ou refuser les éléments, laisser une recommandation, puis convertir le candidat en dignitaire dans une transaction. Les diplômes, langues et expériences sont recopiés au moment de la validation. Les documents généraux validés sont désormais liés au dossier documentaire du dignitaire en conservant un chemin physique unique ; les pièces en attente ou rejetées ne sont pas transférées. La suppression ultérieure d'une référence ou de l'ancien candidat ne supprime pas un fichier ou une photo encore utilisé par le dignitaire.

### Traçabilité

Statut : **Conforme techniquement pour les écritures HTTP**

Les anciennes et nouvelles valeurs restent enregistrées par les contrôleurs métier déjà instrumentés. Une couche centrale couvre maintenant toutes les autres écritures HTTP, enrichit chaque trace avec le contexte de requête et évite les doublons. L'écriture métier et son audit partagent la même transaction : une réponse en erreur annule les changements. L'extension de cette garantie aux commandes planifiées reste à réaliser.

### Conjoints

Statut : **Conforme techniquement pour la chronologie**

Le CRUD, la fin d'union et la chronologie existent. Un test transactionnel garantit que la clôture ajoute un événement sans supprimer l'union initiale ; l'arbre familial visuel reste soumis à confirmation métier.

### Nominations et affectations

Statut : **Partiel avancé**

Les deux ressources disposent d'un CRUD, d'une clôture et d'un audit. La gestion des PV est maintenant implémentée et testée ; il reste à empêcher la destruction de l'historique et à prouver les rappels en environnement opérationnel.

## 6. Vérifications exécutées

| Vérification | Résultat | Interprétation |
|---|---|---|
| Extraction des deux DOCX | Réussie | Les exigences ont été lues, y compris les tableaux et paragraphes |
| Extraction du PDF | Réussie | Le PDF reprend le compte rendu Word et est lui aussi dupliqué |
| `npm run build` | Réussi le 4 octobre 2026 | Nuxt 3.21.4 produit le client et le serveur Nitro, y compris les pages PV et archives |
| Avertissement Nuxt | Présent | Doublon `useDebounce.js` / `useDebounce.ts`, la version JS est ignorée |
| `php artisan test --filter=PvManagementTest` | 3 tests réussis, 19 assertions | Authentification, CRUD/archivage/restauration et blocage de suppression d'un PV lié couverts |
| `php artisan test --filter=ExperienceJustificatifTest` | 4 tests réussis, 26 assertions | Authentification, PDF valide, rejet non-PDF, remplacement, téléchargement et suppression couverts |
| Tests cumulés PV + justificatif d'expérience | 7 tests réussis, 45 assertions | Aucune régression entre les deux premières priorités critiques |
| `php artisan test --filter=DecorationAttributionTest` | 4 tests réussis, 22 assertions | Stockage privé, empreinte, lien poste-dignitaire, PDF, téléchargement et protections couverts |
| Tests cumulés priorités 1.1 à 1.3 | 11 tests réussis, 67 assertions | Aucune régression entre PV, expériences et décorations |
| Tests cumulés priorités 1.1 à 1.4 | 13 tests réussis, 109 assertions | Archivage/restauration du noyau historique et conservation des relations et fichiers validés sans régression |
| `php artisan test --filter=CandidateDocumentTransferTest` | 2 tests réussis, 14 assertions | Sélection des pièces validées, liaison sans copie, traçabilité et conservation après suppression contrôlées |
| Tests cumulés priorités 1.1 à 1.5 | 15 tests réussis, 123 assertions | Aucune régression entre les cinq priorités critiques |
| `php artisan test --filter=CentralAuditTest` | 4 tests réussis, 22 assertions | Trace automatique, enrichissement sans doublon, exclusion des échecs et rollback atomique vérifiés |
| Tests cumulés priorités 1.1 à 1.6 | 19 tests réussis, 145 assertions | Aucune régression entre les six priorités critiques |
| `php artisan test --filter=DashboardMetricsTest` | 2 tests réussis, 21 assertions | Totaux, répartitions, listes récentes et validation du statut militaire couverts |
| Tests cumulés priorités 1.1 à 2.1 | 21 tests réussis, 166 assertions | Aucune régression sur les priorités déjà traitées |
| `php artisan test --filter=AdvancedSearchTest` | 3 tests réussis, 28 assertions | Combinaison des critères texte/relations, filtres booléens, pagination, options et authentification couvertes |
| Tests cumulés priorités 1.1 à 2.2 | 24 tests réussis, 194 assertions | Aucune régression sur les priorités déjà traitées |
| Build complet Nuxt du 5 octobre 2026 | Réussi | La page `/recherche`, son intégration à la barre de recherche et le serveur Nitro sont compilés ; avertissements non bloquants sur `useDebounce` et une dépendance |
| `php artisan test --filter=DignitaireChronologyTest` | 3 tests réussis, 22 assertions | Tri familial, conservation d'une union terminée, regroupement académique, expériences en cours et authentification couverts |
| Tests cumulés priorités 1.1 à 2.3 | 27 tests réussis, 216 assertions | Aucune régression sur les priorités déjà traitées |
| Build Nuxt avec chronologies du 5 octobre 2026 | Réussi, code de sortie 0 | La route `/dignitaires/{id}/chronologie`, le composant de timeline et l'accès depuis la fiche sont compilés dans Nitro |
| `php artisan test --filter=AcademicReferenceTest` | 4 tests réussis, 34 assertions | CRUD, doublons, protection des références utilisées et authentification couverts |
| Tests cumulés priorités 1.1 à 2.4 | 31 tests réussis, 250 assertions | Aucune régression sur les priorités déjà traitées |
| Build Nuxt avec Domaines et Établissements du 5 octobre 2026 | Artefact Nitro régénéré et syntaxe serveur valide | Les pages `/domaines` et `/etablissements` et leur composant partagé sont compilés ; le wrapper a expiré après 249 secondes avant de restituer le code de sortie |
| `php artisan test --filter=LanguageFamilyTest` | 3 tests réussis, 24 assertions | Famille optionnelle, CRUD/recherche, authentification et restriction aux quatre niveaux métier couverts |
| Tests cumulés priorités 1.1 à 2.5 | 34 tests réussis, 274 assertions | Aucune régression sur les priorités déjà traitées |
| Build Nuxt du module Langues du 5 octobre 2026 | Réussi, code de sortie 0 | Le formulaire aligné sur les niveaux métier et le serveur Nitro sont compilés |
| `php artisan test --filter=SemiAnnualReportTest` | 3 tests réussis, 19 assertions | Bornes des deux semestres, PDF, archivage, email, planification et cohérence des diplômes couverts |
| Tests cumulés priorités 1.1 à 2.6 | 37 tests réussis, 293 assertions | Aucune régression sur les priorités déjà traitées |
| Build Nuxt du module Rapports du 5 octobre 2026 | Réussi, code de sortie 0 | Le filtre semestriel, la page Rapports et le serveur Nitro sont compilés |
| Migration `add_soft_deletes_to_historical_records` | Exécutée, lot 36 | `deleted_at` ajouté aux six tables historiques ciblées |
| Migration `link_candidate_documents_to_dignitaries` | Exécutée | Source candidat unique et clé étrangère avec mise à null lors de la suppression |
| Migration `add_request_context_to_audit_logs` | Exécutée, lot 38 | Identifiant de requête, méthode, chemin, statut, IP et agent utilisateur disponibles |
| Migration `add_military_status_to_dignitaire` | Exécutée | Statut militaire explicite, grade obligatoire et index de comptage ajoutés sans modifier les valeurs existantes |
| Build dashboard du 7 octobre 2026 | Réussi, code de sortie 0 | Client et serveur Nitro compilés ; avertissements non bloquants sur `useDebounce` et une dépendance |
| Routes `/api/pvs` | 8 routes actives | Liste, création, consultation, modification, suppression, archivage, restauration et téléchargement |
| Rendu HTTP `/pvs` du build de production | HTTP 200 | La page est bien produite par Nitro ; la capture visuelle reste à faire car le service de contrôle Windows était indisponible |
| Connexion MySQL | Réussie après démarrage de MAMP | Base `gestion_dignitaire`, 46 tables accessibles |
| `php artisan migrate:status` | Corrigé et vérifié | Toutes les migrations sont maintenant enregistrées comme exécutées ; `php artisan migrate --force` répond `Nothing to migrate` |
| Données principales | Lisibles | 15 dignitaires, 3 candidats, 5 nominations, 1 affectation, 1 conjoint, 5 diplômes et 5 expériences |
| Intégrité géographique ciblée | Réussie | 40 pays, 206 régions, 586 villes, aucune ville sans pays et aucun pays sans région |
| Accueil Laravel | HTTP 200 | Serveur d'audit temporaire arrêté après contrôle |
| `/api/public/stats` | HTTP 200 | 15 dignitaires et 40 pays annoncés |
| `/api/public/pays` et `/api/public/villes` | HTTP 200 | Référentiels publics accessibles |
| `/api/dashboard` sans jeton | HTTP 401 | Protection Sanctum active |
| Tests frontend | Absents | Aucun script de test dans `package.json` |
| Parcours authentifiés automatisés | Exécutés dans des transactions | Les écritures testées sont annulées après chaque scénario ; la recette visuelle navigateur reste à effectuer |

## 7. Plan de correction recommandé

### Priorité 0 - sécuriser la recette

Statut : **terminée le 2 octobre 2026**.

1. Un dump de sécurité validé a été créé avant toute écriture : 46 tables, 42 blocs `INSERT`, empreinte SHA-256 conservée.
2. Les trois migrations de socle déjà couvertes par le dump ont été enregistrées dans l'historique sans recréer les tables.
3. Les 20 index métier absents ont été ajoutés.
4. `pays_naissance_id` et `ville_naissance_custom` ont été ajoutés aux candidats ; la migration adapte maintenant son type à l'identifiant historique de `pays` et la clé étrangère est valide.
5. Les six sous-fonctions Conjoint, Affectation, Candidature, Rapports & Exports, Journal des actions et Paramètres (OTP) ont été ajoutées.
6. La migration consolidée de 2027 a été neutralisée sur un schéma existant et son rollback ne peut plus supprimer le socle.
7. Les 46 tables et tous les comptes métier contrôlés sont restés inchangés.
8. `php artisan migrate --force` répond désormais `Nothing to migrate` et les deux tests Laravel existants passent.

### Priorité 1 - exigences métier critiques

1. **PV : validé techniquement le 4 octobre 2026.** Build Nuxt réussi, 8 routes actives, 3 tests/19 assertions réussis, migrations appliquées et données métier intactes. Contrôle visuel manuel restant.
2. **Justificatif PDF des expériences : validé techniquement le 4 octobre 2026.** Upload/remplacement, restriction PDF 10 Mo, téléchargement autorisé, nettoyage des fichiers, interface, build et 4 tests/26 assertions réussis.
3. **Attestation de décoration : validée techniquement le 4 octobre 2026.** Attribution enrichie, poste, stockage privé, PDF 10 Mo, empreinte SHA-256, téléchargement autorisé, interface, build et 4 tests/22 assertions réussis.
4. **Historique critique : validé techniquement le 4 octobre 2026.** Suppression logique et restauration sur six ressources, documents et relations conservés, inventaire `/admin/archives`, migration appliquée, build réussi et tests cumulés à 13 tests/109 assertions. La classification des ressources secondaires et la politique de conservation formelle restent à valider.
5. **Documents candidat : validé techniquement le 4 octobre 2026.** Les pièces validées sont liées sans copie physique au dignitaire, les pièces en attente ou rejetées restent exclues, la source est traçable et les fichiers/photos partagés sont protégés. Build réussi et 2 tests/14 assertions dédiés, soit 15 tests/123 assertions cumulés.
6. **Audit central : validé techniquement le 4 octobre 2026.** Toutes les écritures API réussies produisent une trace, les journaux détaillés sont enrichis sans doublon, les erreurs annulent la transaction et l'interface affiche le contexte HTTP. Build réussi et 4 tests/22 assertions dédiés, soit 19 tests/145 assertions cumulés. Les commandes planifiées restent à intégrer à cette garantie.

### Priorité 2 - complétude fonctionnelle

1. **Indicateurs du dashboard : validés techniquement le 7 octobre 2026.** Totaux nominations/militaires, quatre répartitions supplémentaires, trois listes récentes, exclusion des archives et saisie contrôlée du grade militaire. 2 tests/21 assertions dédiés, soit 21 tests/166 assertions cumulés. La récurrence reste en attente d'une définition métier.
2. **Recherche multicritère : validée techniquement le 5 octobre 2026.** Neuf critères combinables en ET, options dynamiques, pagination, contrôle de permission et exclusion des archives. 3 tests/28 assertions dédiés, soit 24 tests/196 assertions cumulés, et build Nuxt réussi. La recette visuelle navigateur reste à effectuer ; la pertinence approximative concerne encore la recherche rapide.
3. **Chronologies familiale, académique et professionnelle : validées techniquement le 5 octobre 2026.** Vue consolidée par dignitaire, événements familiaux historiques, diplômes triés et regroupés par niveau, expériences triées avec statut `En cours`. 3 tests/22 assertions dédiés, soit 27 tests/218 assertions cumulés, et build Nuxt réussi. La recette visuelle navigateur reste à effectuer.
4. **Référentiels Domaines et Établissements : validés techniquement le 5 octobre 2026.** Deux pages de gestion, CRUD complet, recherche locale, enrichissement établissement-ville, prévention des doublons et blocage des suppressions référencées. 4 tests/34 assertions dédiés, soit 31 tests/252 assertions cumulés. Artefact Nitro régénéré et composants compilés ; recette visuelle navigateur restante.
5. **Famille linguistique : support validé techniquement le 5 octobre 2026, peuplement en attente métier.** Le champ optionnel existant est couvert de bout en bout par le CRUD, l'affichage et la recherche. Les niveaux sont désormais strictement alignés sur Débutant, Moyen, Courant et Bilingue. 3 tests/24 assertions dédiés, soit 34 tests/276 assertions cumulés, et build Nuxt réussi. Les documents disent seulement que le classement a été évoqué : les sept langues restent donc volontairement sans famille jusqu'à validation d'une taxonomie.
6. **Période semestrielle : validée techniquement le 5 octobre 2026.** Calcul du dernier semestre terminé, génération PDF, archivage, email, filtre d'interface et planification les 1er janvier et 1er juillet. Les diplômes ne sont pas attribués artificiellement à un semestre puisqu'ils ne stockent qu'une année. 3 tests/19 assertions dédiés, soit 37 tests/295 assertions cumulés, et build Nuxt réussi.

### Priorité 3 - qualité et exploitation

1. Écrire des tests d'autorisation pour tous les rôles.
2. Écrire des tests CRUD et de validation pour chaque module.
3. Écrire des tests de conversion candidat-dignitaire et de non-régression documentaire.
4. Ajouter des tests E2E sur les parcours critiques.
5. Superviser scheduler, queue et emails.
6. Supprimer le doublon `useDebounce.js`/`useDebounce.ts` après vérification des imports.

## 8. Scénarios minimaux de recette de bout en bout

1. Inscription candidat, OTP, ajout CV, diplôme, langue et expérience.
2. Connexion administrateur, validation et rejet séparés de pièces.
3. Validation du candidat et contrôle de la création exacte du dignitaire.
4. Vérification du transfert de chaque donnée et document.
5. Création puis fin d'union d'un conjoint, sans perte d'historique.
6. Création d'un enfant et affichage sur la fiche.
7. Création d'un diplôme avec justificatif, puis téléchargement autorisé.
8. Création d'une expérience avec justificatif après correction.
9. Création d'une nomination, email, clôture et rappel d'expiration.
10. Création d'une affectation distincte de la nationalité.
11. Attribution d'une décoration liée au poste avec attestation après correction.
12. Vérification lecture seule, écriture et suppression pour chaque rôle.
13. Vérification de l'audit : acteur, date, ancienne valeur et nouvelle valeur.
14. Export PDF/Excel et import Excel avec lignes valides et invalides.
15. Recherche globale et recherche multicritère après correction.
16. Génération manuelle puis planifiée de chaque rapport périodique.

## 9. Verdict final

Le projet n'est plus un simple prototype CRUD : les principaux domaines métier et plusieurs exigences avancées sont réellement représentés dans le code. Le démarrage de MAMP a permis de confirmer que la base de 46 tables et les endpoints publics fonctionnent. La priorité 0 relative aux migrations est maintenant résolue. Le projet ne peut cependant pas encore être déclaré totalement conforme ou entièrement fonctionnel de bout en bout : les lacunes documentaires, l'absence de tests métier, les fonctionnalités partielles citées ci-dessus et l'absence de recette authentifiée isolée empêchent une validation finale.

La priorité 2 est désormais traitée. La prochaine étape est la priorité 3.1 : écrire une matrice de tests d'autorisation couvrant tous les rôles et les méthodes HTTP critiques.
