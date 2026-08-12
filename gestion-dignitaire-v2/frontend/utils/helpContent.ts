import type { TourStep } from '~/composables/useGuidedTour'
import type { HelpContent } from '~/composables/useHelpPanel'

/**
 * Visite par défaut pour une page liste+modale classique (la grande
 * majorité des pages admin partagent ce même squelette : en-tête, barre de
 * filtres, bouton "Ajouter", tableau, actions de ligne). Les pages qui ont
 * une sémantique particulière définissent leurs propres étapes dans
 * HELP_CONTENT ci-dessous plutôt que d'utiliser ce générateur.
 */
export function buildDefaultTourSteps(pageLabel: string): TourStep[] {
  return [
    {
      target: '[data-tour="page-header"]',
      title: pageLabel,
      content: `Bienvenue sur l'écran ${pageLabel}. Cette courte visite vous montre les éléments clés de cette page.`
    },
    {
      target: '[data-tour="filters"]',
      title: 'Rechercher et filtrer',
      content: 'Utilisez la recherche et les filtres pour retrouver rapidement un élément dans la liste, sans avoir à tout parcourir.'
    },
    {
      target: '[data-tour="add-button"]',
      title: 'Ajouter un élément',
      content: 'Ce bouton ouvre un formulaire pour créer un nouvel enregistrement. Il n\'apparaît que si vous avez les droits d\'écriture sur cette fonction.'
    },
    {
      target: '[data-tour="table"]',
      title: 'La liste',
      content: 'Chaque ligne représente un enregistrement existant. Le tableau se met à jour automatiquement après chaque ajout, modification ou suppression.'
    },
    {
      target: '[data-tour="row-actions"]',
      title: 'Actions rapides',
      content: 'Consultez, modifiez ou supprimez un élément directement depuis ces boutons, selon vos droits d\'accès.'
    }
  ]
}

export const HELP_CONTENT: Record<string, HelpContent> = {
  dashboard: {
    title: 'Tableau de bord',
    intro: 'Votre point de départ : un coup d\'œil sur les chiffres clés, l\'activité récente et les derniers dignitaires enregistrés.',
    sections: [
      { heading: 'Cartes statistiques', body: 'Chaque carte est cliquable ("Voir") et vous amène directement à la liste correspondante.' },
      { heading: 'Graphique de répartition', body: 'Changez le critère (genre, région, poste, statut, tendance mensuelle) via le menu déroulant au-dessus du graphique.' },
      { heading: 'Ma progression', body: 'La liste de prise en main vous guide vers les actions essentielles pour démarrer ; elle se coche automatiquement au fil de votre navigation.' }
    ]
  },

  dignitaires: {
    title: 'Dignitaires',
    intro: 'La fiche dignitaire est le cœur de l\'application : chaque enregistrement centralise l\'identité, les postes, décorations, diplômes, expériences, conjoints et enfants d\'une personne.',
    sections: [
      { heading: 'Matricule', body: 'Généré automatiquement à la création. S\'il provient d\'une candidature sans matricule fourni, un matricule provisoire est attribué.' },
      { heading: 'Export', body: 'Les boutons PDF/Excel exportent la liste filtrée actuelle, pas uniquement la page affichée.' }
    ],
    tourId: 'dignitaires',
    tourSteps: buildDefaultTourSteps('Dignitaires')
  },

  'dignitaire-detail': {
    title: 'Fiche dignitaire',
    intro: 'Cette fiche régroupe toutes les informations liées à un dignitaire, organisées en sections.',
    sections: [
      { heading: 'Complétude du dossier', body: 'La barre de progression indique la part des informations importantes déjà renseignées (photo, documents, conjoints, enfants, diplômes, expériences, nominations).' },
      { heading: 'Documents', body: 'Le module de documents accepte plusieurs fichiers par glisser-déposer ; supprimer un document ne supprime que sa fiche, pas nécessairement le fichier physique côté serveur.' }
    ]
  },

  postes: {
    title: 'Postes & Entités',
    intro: 'Cet écran combine deux onglets : Postes (fonctions occupées) et Entités (organismes/structures). Un poste rattaché à une ville génère automatiquement une affectation liée.',
    sections: [
      { heading: 'Lien avec les affectations', body: 'Créer, modifier ou clôturer un poste ayant une ville crée, met à jour ou clôture automatiquement l\'affectation correspondante — visible dans le module Affectations avec le badge "Auto".' },
      { heading: 'Clôturer un poste', body: 'La clôture ne supprime pas le poste : elle marque sa fin (date de fin) sans effacer l\'historique.' }
    ],
    tourId: 'postes',
    tourSteps: [
      {
        target: '[data-tour="page-header"]',
        title: 'Postes & Entités',
        content: 'Deux onglets sur un même écran : Postes et Entités. Cette visite couvre l\'onglet Postes.'
      },
      {
        target: '[data-tour="filters"]',
        title: 'Rechercher un poste',
        content: 'Filtrez par intitulé, entité ou statut pour retrouver rapidement un poste.'
      },
      {
        target: '[data-tour="add-button"]',
        title: 'Créer un poste',
        content: 'Si vous renseignez une ville, une affectation liée sera générée automatiquement — pas besoin de la créer manuellement dans le module Affectations.'
      },
      {
        target: '[data-tour="table"]',
        title: 'Liste des postes',
        content: 'Chaque ligne représente un poste. Un poste clôturé reste visible pour l\'historique mais n\'apparaît plus comme actif.'
      },
      {
        target: '[data-tour="row-actions"]',
        title: 'Clôturer ou modifier',
        content: 'La clôture d\'un poste répercute automatiquement la fin de l\'affectation liée.'
      }
    ]
  },

  affectations: {
    title: 'Affectations',
    intro: 'Une affectation relie un dignitaire à une ville/fonction sur une période donnée.',
    sections: [
      { heading: 'Badge "Auto"', body: 'Une affectation marquée "Auto" a été générée automatiquement depuis un poste (module Postes). Elle se met à jour ou se clôture toute seule quand le poste change — inutile de la modifier manuellement dans ce cas.' },
      { heading: 'Affectations manuelles', body: 'Les affectations sans badge sont créées directement ici et restent indépendantes des postes.' }
    ],
    tourId: 'affectations',
    tourSteps: buildDefaultTourSteps('Affectations')
  },

  entites: {
    title: 'Entités',
    intro: 'Les entités représentent les organismes/structures (ministères, directions, etc.) auxquels un poste peut être rattaché.',
    sections: [
      { heading: 'Logo et contact', body: 'Le logo et les informations de contact d\'une entité apparaissent sur les documents générés (fiches, exports) qui la mentionnent.' }
    ],
    tourId: 'entites',
    tourSteps: buildDefaultTourSteps('Entités')
  },

  candidatures: {
    title: 'Candidatures',
    intro: 'Liste des candidatures soumises par les candidats. Cliquez sur une candidature pour l\'examiner en détail.',
    sections: [
      { heading: 'Statuts', body: 'Une candidature passe de "en attente" à "validée" ou "refusée" ; seule la validation crée automatiquement un dignitaire.' }
    ],
    tourId: 'candidatures',
    tourSteps: [
      {
        target: '[data-tour="page-header"]',
        title: 'Candidatures',
        content: 'Les candidatures sont soumises par les candidats eux-mêmes depuis leur espace — il n\'y a pas de création manuelle ici.'
      },
      {
        target: '[data-tour="filters"]',
        title: 'Rechercher et filtrer',
        content: 'Filtrez par statut (en attente, validée, refusée) ou recherchez par nom, email ou matricule.'
      },
      {
        target: '[data-tour="table"]',
        title: 'La liste',
        content: 'Chaque ligne résume une candidature. Cliquez sur "Consulter" pour l\'examiner en détail et prendre une décision.'
      }
    ]
  },

  'candidature-detail': {
    title: 'Examen d\'une candidature',
    intro: 'Trois actions distinctes sont disponibles sur cet écran — ne les confondez pas.',
    sections: [
      { heading: 'Valider', body: 'Crée automatiquement un dignitaire à partir des informations de la candidature. Action définitive.' },
      { heading: 'Refuser', body: 'Exige un motif d\'au moins 10 caractères, envoyé au candidat par email. Aucun dignitaire n\'est créé.' },
      { heading: 'Recommandation', body: 'Un canal séparé : un message libre envoyé au candidat par email et dans son espace, sans changer le statut de la candidature. Utile pour demander un complément d\'information avant de décider.' }
    ]
  },

  parametres: {
    title: 'Paramètres (Super Admin)',
    intro: 'Réservé au rôle Super Administrateur. Contrôle l\'authentification à deux facteurs (OTP) par email et les notifications de candidature.',
    sections: [
      { heading: 'OTP connexion admin', body: 'Si activé, tous les comptes administrateurs doivent saisir un code reçu par email à chaque connexion.' },
      { heading: 'OTP connexion candidat', body: 'Si activé, les candidats doivent aussi saisir un code à la connexion. La vérification d\'email à l\'inscription reste obligatoire dans tous les cas, indépendamment de ce réglage.' },
      { heading: 'Notifications de candidature', body: 'Si activé, tous les Administrateurs et Super Administrateurs reçoivent un email à chaque nouvelle candidature, validation ou refus.' }
    ]
  },

  rapports: {
    title: 'Rapports & Exports',
    intro: 'Générez des exports ponctuels (PDF/Excel) ou consultez la configuration des rapports périodiques automatiques.',
    sections: [
      { heading: 'Rapports périodiques', body: 'Ces rapports sont envoyés par email et archivés automatiquement, sans action manuelle une fois configurés.' }
    ],
    tourId: 'rapports',
    tourSteps: buildDefaultTourSteps('Rapports & Exports')
  },

  'audit-logs': {
    title: 'Journal des actions',
    intro: 'Trace de toutes les créations, modifications et suppressions effectuées dans l\'application, avec l\'auteur et l\'horodatage.',
    sections: [
      { heading: 'Filtrer', body: 'Filtrez par utilisateur, type d\'action ou période pour retrouver un événement précis.' }
    ],
    tourId: 'audit-logs',
    tourSteps: buildDefaultTourSteps('Journal des actions')
  },

  'admin-create': {
    title: 'Gestion des comptes',
    intro: 'Créez de nouveaux comptes administrateurs et gérez les droits d\'accès (rôle, fonctions, sous-fonctions).',
    sections: [
      { heading: 'Rôles', body: 'Le rôle détermine l\'accès global ; les sous-fonctions affinent ensuite les droits de lecture/écriture par module.' }
    ],
    tourId: 'admin-create',
    tourSteps: buildDefaultTourSteps('Gestion des comptes')
  },

  nominations: {
    title: 'Nominations',
    intro: 'Historique des nominations d\'un dignitaire à un poste, avec la preuve de nomination associée.',
    sections: [
      { heading: 'Preuve de nomination', body: 'Vous pouvez joindre le document officiel (décret, arrêté) et préciser son type et l\'autorité signataire.' }
    ],
    tourId: 'nominations',
    tourSteps: buildDefaultTourSteps('Nominations')
  },

  decorations: {
    title: 'Décorations',
    intro: 'Répertoire des décorations et distinctions pouvant être attribuées aux dignitaires.',
    sections: [],
    tourId: 'decorations',
    tourSteps: buildDefaultTourSteps('Décorations')
  },

  diplomes: {
    title: 'Diplômes',
    intro: 'Répertoire des diplômes, avec le type et l\'établissement d\'obtention.',
    sections: [
      { heading: 'Document PDF', body: 'Chaque diplôme peut avoir une copie PDF jointe, consultable depuis la fiche du dignitaire.' }
    ],
    tourId: 'diplomes',
    tourSteps: buildDefaultTourSteps('Diplômes')
  },

  experiences: {
    title: 'Expériences',
    intro: 'Parcours professionnel antérieur d\'un dignitaire (hors postes actuels gérés dans le module Postes).',
    sections: [],
    tourId: 'experiences',
    tourSteps: buildDefaultTourSteps('Expériences')
  },

  conjoints: {
    title: 'Conjoints',
    intro: 'Gestion globale des conjoints rattachés aux dignitaires.',
    sections: [],
    tourId: 'conjoints',
    tourSteps: buildDefaultTourSteps('Conjoints')
  },

  enfants: {
    title: 'Enfants',
    intro: 'Gestion des enfants rattachés aux dignitaires.',
    sections: [],
    tourId: 'enfants',
    tourSteps: buildDefaultTourSteps('Enfants')
  },

  structures: {
    title: 'Structures',
    intro: 'Table de référence utilisée dans les formulaires (parcours, expériences).',
    sections: [],
    tourId: 'structures',
    tourSteps: buildDefaultTourSteps('Structures')
  },

  langues: {
    title: 'Langues',
    intro: 'Répertoire des langues disponibles pour le module Langues parlées.',
    sections: [],
    tourId: 'langues',
    tourSteps: buildDefaultTourSteps('Langues')
  },

  'langues-parlees': {
    title: 'Langues parlées',
    intro: 'Relie un dignitaire aux langues qu\'il parle, avec son niveau.',
    sections: [
      { heading: 'Lien avec Langues', body: 'La liste des langues proposées ici provient du répertoire du module Langues — ajoutez-y d\'abord une langue si elle est absente.' }
    ],
    tourId: 'langues-parlees',
    tourSteps: buildDefaultTourSteps('Langues parlées')
  },

  pays: {
    title: 'Pays',
    intro: 'Table de référence géographique, base du filtrage en cascade Pays → Ville utilisé dans les formulaires.',
    sections: [],
    tourId: 'pays',
    tourSteps: buildDefaultTourSteps('Pays')
  },

  villes: {
    title: 'Villes',
    intro: 'Table de référence géographique, rattachée à un pays.',
    sections: [
      { heading: 'Lien avec Pays', body: 'Chaque ville est rattachée à un pays ; les formulaires ailleurs dans l\'app filtrent d\'abord par pays avant de proposer une ville.' }
    ],
    tourId: 'villes',
    tourSteps: buildDefaultTourSteps('Villes')
  },

  regions: {
    title: 'Régions',
    intro: 'Table de référence des régions administratives.',
    sections: [],
    tourId: 'regions',
    tourSteps: buildDefaultTourSteps('Régions')
  },

  'candidat-dashboard': {
    title: 'Mon espace candidat',
    intro: 'Ce tableau de bord centralise votre dossier de candidature : informations, documents, diplômes, expériences, langues et échanges avec l\'administration.',
    sections: [
      { heading: 'Progression du dossier', body: 'Le pourcentage se met à jour automatiquement selon les sections que vous complétez — ce n\'est pas une note, juste un repère pour savoir ce qu\'il reste à faire.' },
      { heading: 'Modification possible tant que "En attente"', body: 'Vous pouvez ajouter/modifier vos informations tant que votre dossier n\'a pas été traité. Une fois validé ou refusé, le dossier est verrouillé.' },
      { heading: 'Notifications', body: 'La cloche en haut à droite affiche les recommandations et messages envoyés par l\'administration à propos de votre dossier.' }
    ],
    tourId: 'candidat-dashboard',
    tourSteps: [
      {
        target: '[data-tour="candidat-header"]',
        title: 'Bienvenue sur votre espace',
        content: 'Ici s\'affichent votre statut de candidature (en attente, validée, refusée) et vos informations principales.'
      },
      {
        target: '[data-tour="candidat-progress"]',
        title: 'Progression du dossier',
        content: 'Ce pourcentage se calcule automatiquement selon les sections déjà complétées — un repère, pas une note.'
      },
      {
        target: '[data-tour="candidat-nav"]',
        title: 'Navigation',
        content: 'Ce menu vous amène directement à chaque section de votre dossier : profil, documents, langues, diplômes, expériences, chronologie.'
      },
      {
        target: '[data-tour="candidat-notifications"]',
        title: 'Messages de l\'administration',
        content: 'Les recommandations ou remarques laissées sur votre dossier apparaissent ici, avec une pastille rouge quand il y a du nouveau.'
      }
    ]
  },

  'candidat-profil': {
    title: 'Mon profil',
    intro: 'Modifiez vos informations personnelles ou votre mot de passe.',
    sections: [
      { heading: 'Deux onglets', body: '"Informations" pour vos données personnelles, "Mot de passe" pour la sécurité de votre compte — accessible même si votre dossier est verrouillé.' },
      { heading: 'Dossier verrouillé', body: 'Si votre candidature a été validée ou refusée, les champs d\'informations personnelles ne sont plus modifiables ; le changement de mot de passe reste toujours possible.' }
    ]
  },

  'candidature-inscription': {
    title: 'Formulaire de candidature',
    intro: 'L\'inscription se fait en 4 étapes. Rien n\'est enregistré tant que vous n\'avez pas terminé l\'étape 4 et validé le code reçu par email.',
    sections: [
      { heading: '4 étapes', body: 'Identité, coordonnées, informations complémentaires puis récapitulatif — vous pouvez revenir en arrière avant l\'envoi final.' },
      { heading: 'Vérification par email', body: 'Une fois le formulaire envoyé, un code de vérification est exigé par email avant la création effective du compte.' }
    ]
  }
}
