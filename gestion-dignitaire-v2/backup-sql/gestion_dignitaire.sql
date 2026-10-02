-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 01 sep. 2026 à 16:57
-- Version du serveur : 5.7.24
-- Version de PHP : 8.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_dignitaire`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `candidat_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('nouveau_document','nouveau_diplome','nouvelle_experience','modification_profil') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'modification_profil',
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  `lu_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `admin_notifications`
--

INSERT INTO `admin_notifications` (`id`, `candidat_id`, `type`, `titre`, `message`, `lu`, `lu_le`, `created_at`, `updated_at`) VALUES
(1, 4, 'nouveau_document', 'Nouveau document ajouté', 'christian georges a ajouté un document de type \"cv\".', 0, NULL, '2026-08-05 12:32:48', '2026-08-05 12:32:48'),
(2, 4, 'nouveau_document', 'Nouveau document ajouté', 'christian georges a ajouté un document de type \"cv\".', 1, '2026-08-05 12:36:17', '2026-08-05 12:33:41', '2026-08-05 12:36:17'),
(3, 4, 'nouveau_document', 'Document validé', 'Votre document \"cv\" a été validé par l\'administration.', 0, NULL, '2026-08-05 12:36:32', '2026-08-05 12:36:32');

-- --------------------------------------------------------

--
-- Structure de la table `affectations`
--

CREATE TABLE `affectations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `poste_id` int(11) DEFAULT NULL,
  `pays_id` int(11) NOT NULL,
  `ville_id` int(11) DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `type_affectation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nature` enum('principale','mission_temporaire') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'principale',
  `statut` enum('en_cours','terminee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_cours',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `affectations`
--

INSERT INTO `affectations` (`id`, `dignitaire_id`, `poste_id`, `pays_id`, `ville_id`, `date_debut`, `date_fin`, `type_affectation`, `nature`, `statut`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 2, 2, '2026-04-16', NULL, 'Autre', 'principale', 'en_cours', '2026-08-04 12:29:42', '2026-08-04 12:29:42');

-- --------------------------------------------------------

--
-- Structure de la table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `auditable_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `causer_type`, `causer_id`, `causer_label`, `action`, `auditable_type`, `auditable_id`, `auditable_label`, `old_values`, `new_values`, `created_at`) VALUES
(1, 'App\\Models\\User', 11, 'admin1', 'created', 'Dignitaire', 19, 'AuditPrenom AuditNom', NULL, '{\"nom\": \"AuditNom\", \"prenom\": \"AuditPrenom\", \"matricule\": \"AUDIT-TEST-1\"}', '2026-07-01 21:13:47'),
(2, 'App\\Models\\User', 11, 'admin1', 'updated', 'Dignitaire', 19, 'AuditPrenom AuditNomModifie', '{\"id\": 19, \"nip\": null, \"nom\": \"AuditNom\", \"genre\": null, \"photo\": null, \"prenom\": \"AuditPrenom\", \"adresse\": null, \"casierJud\": null, \"matricule\": \"AUDIT-TEST-1\", \"telephone\": null, \"etat_civil\": null, \"nationalite\": null, \"certificatsMed\": null, \"date_naissance\": null, \"lieu_naissance\": null}', '{\"nom\": \"AuditNomModifie\", \"prenom\": \"AuditPrenom\", \"matricule\": \"AUDIT-TEST-1\"}', '2026-07-01 21:14:31'),
(3, 'App\\Models\\User', 11, 'admin1', 'created', 'Nomination', 6, 'Test Fonction', NULL, '{\"fonction\": \"Test Fonction\", \"dignitaire_id\": \"19\"}', '2026-07-01 21:14:36'),
(4, 'App\\Models\\User', 11, 'admin1', 'updated', 'Nomination', 6, 'Test Fonction Modifiee', '{\"id\": 6, \"pv_id\": null, \"date_fin\": null, \"fonction\": \"Test Fonction\", \"poste_id\": null, \"entite_id\": null, \"date_debut\": \"0000-00-00\", \"dignitaire_id\": 19}', '{\"fonction\": \"Test Fonction Modifiee\", \"dignitaire_id\": \"19\"}', '2026-07-01 21:14:41'),
(5, 'App\\Models\\User', 11, 'admin1', 'deleted', 'Nomination', 6, 'Test Fonction Modifiee', '{\"id\": 6, \"pv_id\": null, \"date_fin\": null, \"fonction\": \"Test Fonction Modifiee\", \"poste_id\": null, \"entite_id\": null, \"date_debut\": \"0000-00-00\", \"dignitaire_id\": 19}', NULL, '2026-07-01 21:14:43'),
(6, 'App\\Models\\User', 11, 'admin1', 'deleted', 'Dignitaire', 19, 'AuditPrenom AuditNomModifie', '{\"id\": 19, \"nip\": null, \"nom\": \"AuditNomModifie\", \"genre\": null, \"photo\": null, \"prenom\": \"AuditPrenom\", \"adresse\": null, \"casierJud\": null, \"matricule\": \"AUDIT-TEST-1\", \"telephone\": null, \"etat_civil\": null, \"nationalite\": null, \"certificatsMed\": null, \"date_naissance\": null, \"lieu_naissance\": null}', NULL, '2026-07-01 21:14:44'),
(7, 'App\\Models\\User', 11, 'admin1', 'validated', 'Candidat', 3, 'Test AuditCandidat2', '{\"statut\": \"en_attente\"}', '{\"statut\": \"valide\", \"dignitaire_id\": 20}', '2026-07-01 21:27:40'),
(8, 'App\\Models\\User', 11, 'admin1', 'validated', 'Candidat', 4, 'Test SansMatricule', '{\"statut\": \"en_attente\"}', '{\"statut\": \"valide\", \"dignitaire_id\": 21}', '2026-07-01 21:40:21'),
(9, 'App\\Models\\User', 11, 'admin1', 'validated', 'Candidat', 5, 'Test AvecMatricule', '{\"statut\": \"en_attente\"}', '{\"statut\": \"valide\", \"dignitaire_id\": 22}', '2026-07-01 21:40:36'),
(10, 'App\\Models\\User', 11, 'admin1', 'created', 'Dignitaire', 23, 'Test NomTest', NULL, '{\"nom\": \"NomTest\", \"prenom\": \"Test\", \"matricule\": \"NOM-TEST-1\"}', '2026-07-01 23:13:19'),
(11, 'App\\Models\\User', 11, 'admin1', 'created', 'Nomination', 7, 'Fonction Test', NULL, '{\"statut\": \"en_cours\", \"fonction\": \"Fonction Test\", \"dignitaire_id\": \"23\", \"numero_decret\": \"DEC-2026-001\"}', '2026-07-01 23:13:22'),
(12, 'App\\Models\\User', 11, 'admin1', 'cloturee', 'Nomination', 7, 'Fonction Test', '{\"id\": 7, \"pv_id\": null, \"statut\": \"en_cours\", \"date_fin\": null, \"fonction\": \"Fonction Test\", \"poste_id\": null, \"entite_id\": null, \"motif_fin\": null, \"date_debut\": \"0000-00-00\", \"dignitaire_id\": 23, \"numero_decret\": \"DEC-2026-001\"}', '{\"statut\": \"terminee\", \"date_fin\": \"2026-07-02\", \"motif_fin\": \"mise_a_disposition\"}', '2026-07-01 23:13:23'),
(13, 'App\\Models\\User', 11, 'admin1', 'created', 'Poste', 16, 'Poste Test', NULL, '{\"statut\": \"en_cours\", \"intitule\": \"Poste Test\", \"dignitaire_id\": \"23\"}', '2026-07-01 23:13:38'),
(14, 'App\\Models\\User', 11, 'admin1', 'cloturee', 'Poste', 16, 'Poste Test', '{\"id\": 16, \"statut\": \"en_cours\", \"date_fin\": null, \"intitule\": \"Poste Test\", \"ville_id\": null, \"entite_id\": null, \"motif_fin\": null, \"date_debut\": null, \"dignitaire_id\": 23}', '{\"statut\": \"terminee\", \"date_fin\": \"2026-06-30\", \"motif_fin\": \"fin_fonction\"}', '2026-07-01 23:13:39'),
(15, 'App\\Models\\User', 18, 'test_gestionnaire', 'created', 'Dignitaire', 16, 'Test Gest', NULL, '{\"nom\": \"Gest\", \"prenom\": \"Test\", \"matricule\": \"PERM-TEST-G\"}', '2026-07-02 09:19:04'),
(16, 'App\\Models\\User', 19, 'test_admin', 'deleted', 'Dignitaire', 16, 'Test Gest', '{\"id\": 16, \"nip\": null, \"nom\": \"Gest\", \"genre\": null, \"photo\": null, \"prenom\": \"Test\", \"adresse\": null, \"casierJud\": null, \"matricule\": \"PERM-TEST-G\", \"telephone\": null, \"etat_civil\": null, \"nationalite\": null, \"certificatsMed\": null, \"date_naissance\": null, \"lieu_naissance\": null}', NULL, '2026-07-02 09:19:10'),
(17, 'App\\Models\\User', 11, 'admin1', 'created', 'Dignitaire', 17, 'StatutPrenom StatutNom', NULL, '{\"nom\": \"StatutNom\", \"prenom\": \"StatutPrenom\", \"matricule\": \"STATUT-TEST-1\"}', '2026-07-02 10:15:20'),
(18, 'App\\Models\\User', 11, 'admin1', 'updated', 'Dignitaire', 17, 'StatutPrenom StatutNom', '{\"id\": 17, \"nip\": null, \"nom\": \"StatutNom\", \"genre\": null, \"photo\": null, \"prenom\": \"StatutPrenom\", \"statut\": \"actif\", \"adresse\": null, \"casierJud\": null, \"matricule\": \"STATUT-TEST-1\", \"telephone\": null, \"etat_civil\": null, \"nationalite\": null, \"certificatsMed\": null, \"date_naissance\": null, \"lieu_naissance\": null}', '{\"nom\": \"StatutNom\", \"prenom\": \"StatutPrenom\", \"statut\": \"retraite\", \"matricule\": \"STATUT-TEST-1\"}', '2026-07-02 10:15:27'),
(19, 'App\\Models\\User', 11, 'admin1', 'validated', 'Candidat', 4, 'Test VerifNotif', '{\"statut\": \"en_attente\"}', '{\"statut\": \"valide\", \"dignitaire_id\": 16}', '2026-07-04 18:42:10'),
(20, 'App\\Models\\User', 11, 'admin1', 'refused', 'Candidat', 5, 'Test VerifNotifRefus', '{\"statut\": \"en_attente\"}', '{\"statut\": \"refuse\", \"motif_refus\": \"Dossier incomplet pour verification test\"}', '2026-07-04 18:48:30'),
(21, 'App\\Models\\User', 11, 'admin1', 'created', 'Nomination', 6, 'Conseiller Test Verif', NULL, '{\"statut\": \"en_cours\", \"date_fin\": \"2026-08-01\", \"fonction\": \"Conseiller Test Verif\", \"date_debut\": \"2026-07-04\", \"dignitaire_id\": 16}', '2026-07-04 18:49:57'),
(22, 'App\\Models\\User', 11, 'admin1', 'created', 'Nomination', 7, 'Conseiller Test Verif 2', NULL, '{\"statut\": \"en_cours\", \"date_fin\": \"2026-07-25\", \"fonction\": \"Conseiller Test Verif 2\", \"entite_id\": null, \"date_debut\": \"2026-07-04\", \"dignitaire_id\": 16}', '2026-07-04 18:54:51'),
(23, 'App\\Models\\User', 11, 'admin1', 'created', 'Conjoint', 1, 'Test VerifConjoint', NULL, '{\"nom\": \"VerifConjoint\", \"genre\": \"F\", \"prenom\": \"Test\", \"statut\": \"actif\", \"profession\": \"Enseignante\", \"date_mariage\": \"2020-01-01\", \"dignitaire_id\": 1, \"est_militaire\": false, \"est_dignitaire\": true, \"fonction_dignitaire\": \"Conseillere\"}', '2026-07-04 23:41:18'),
(24, 'App\\Models\\User', 11, 'admin1', 'updated', 'Conjoint', 1, 'Test2 VerifConjoint', '{\"id\": 1, \"nom\": \"VerifConjoint\", \"email\": null, \"genre\": \"F\", \"photo\": null, \"prenom\": \"Test\", \"statut\": \"actif\", \"adresse\": null, \"employeur\": null, \"telephone\": null, \"created_at\": \"2026-07-05T00:41:18.000000Z\", \"profession\": \"Enseignante\", \"updated_at\": \"2026-07-05T00:41:18.000000Z\", \"date_mariage\": \"2020-01-01T00:00:00.000000Z\", \"lieu_mariage\": null, \"dignitaire_id\": 1, \"est_militaire\": false, \"date_fin_union\": null, \"date_naissance\": null, \"est_dignitaire\": true, \"nationalite_id\": null, \"grade_militaire\": null, \"acte_mariage_path\": null, \"lieu_naissance_id\": null, \"fonction_dignitaire\": \"Conseillere\"}', '{\"nom\": \"VerifConjoint\", \"genre\": \"F\", \"prenom\": \"Test2\"}', '2026-07-04 23:41:34'),
(25, 'App\\Models\\User', 11, 'admin1', 'updated', 'Conjoint', 1, 'Test2 VerifConjoint', '{\"id\": 1, \"nom\": \"VerifConjoint\", \"email\": null, \"genre\": \"F\", \"photo\": null, \"prenom\": \"Test2\", \"statut\": \"actif\", \"adresse\": null, \"employeur\": null, \"telephone\": null, \"created_at\": \"2026-07-05T00:41:18.000000Z\", \"profession\": \"Enseignante\", \"updated_at\": \"2026-07-05T00:41:34.000000Z\", \"date_mariage\": \"2020-01-01T00:00:00.000000Z\", \"lieu_mariage\": null, \"dignitaire_id\": 1, \"est_militaire\": false, \"date_fin_union\": null, \"date_naissance\": null, \"est_dignitaire\": true, \"nationalite_id\": null, \"grade_militaire\": null, \"acte_mariage_path\": null, \"lieu_naissance_id\": null, \"fonction_dignitaire\": \"Conseillere\"}', '{\"statut\": \"divorce\", \"date_fin_union\": \"2026-07-01\"}', '2026-07-04 23:41:35'),
(26, 'App\\Models\\User', 11, 'admin1', 'deleted', 'Conjoint', 1, 'Test2 VerifConjoint', '{\"id\": 1, \"nom\": \"VerifConjoint\", \"email\": null, \"genre\": \"F\", \"photo\": null, \"prenom\": \"Test2\", \"statut\": \"divorce\", \"adresse\": null, \"employeur\": null, \"telephone\": null, \"created_at\": \"2026-07-05T00:41:18.000000Z\", \"profession\": \"Enseignante\", \"updated_at\": \"2026-07-05T00:41:35.000000Z\", \"date_mariage\": \"2020-01-01T00:00:00.000000Z\", \"lieu_mariage\": null, \"dignitaire_id\": 1, \"est_militaire\": false, \"date_fin_union\": \"2026-07-01T00:00:00.000000Z\", \"date_naissance\": null, \"est_dignitaire\": true, \"nationalite_id\": null, \"grade_militaire\": null, \"acte_mariage_path\": null, \"lieu_naissance_id\": null, \"fonction_dignitaire\": \"Conseillere\"}', NULL, '2026-07-04 23:42:37'),
(27, 'App\\Models\\User', 11, 'admin1', 'created', 'DignitaireDocument', 1, 'passeport - Ali BONGO ', NULL, '{\"fichier\": {}, \"nom_document\": \"Passeport test\", \"type_document\": \"passeport\", \"date_expiration\": \"2026-07-15\"}', '2026-07-04 23:59:44'),
(28, 'App\\Models\\User', 11, 'admin1', 'created', 'DignitaireDocument', 2, 'medical - Ali BONGO ', NULL, '{\"fichier\": {}, \"nom_document\": \"Certificat medical test\", \"type_document\": \"medical\", \"date_expiration\": \"2020-01-01\"}', '2026-07-04 23:59:59'),
(29, 'App\\Models\\User', 11, 'admin1', 'created', 'DignitaireDocument', 3, 'diplome - Ali BONGO ', NULL, '{\"fichier\": {}, \"nom_document\": \"Diplome test\", \"type_document\": \"diplome\"}', '2026-07-05 00:00:00'),
(30, 'App\\Models\\User', 11, 'admin1', 'deleted', 'DignitaireDocument', 1, 'passeport - test.pdf', '{\"id\": 1, \"extension\": \"pdf\", \"created_at\": \"2026-07-05T00:59:44.000000Z\", \"updated_at\": \"2026-07-05T00:59:44.000000Z\", \"description\": null, \"nom_fichier\": \"test.pdf\", \"nom_document\": \"Passeport test\", \"date_emission\": null, \"dignitaire_id\": 1, \"type_document\": \"passeport\", \"chemin_fichier\": \"dignitaires/documents/QIO0LEikbLwo2Tg5VB3vv8OuuxisXGqotKVhDRdP.pdf\", \"taille_fichier\": 62, \"date_expiration\": \"2026-07-15T00:00:00.000000Z\", \"numero_document\": null, \"organisme_emetteur\": null}', NULL, '2026-07-05 00:02:23'),
(31, 'App\\Models\\User', 11, 'admin1', 'created', 'Diplome', 6, 'Diplome Test Verif', NULL, '{\"type\": \"Master\", \"intitule\": \"Diplome Test Verif\", \"dignitaire_id\": \"1\", \"etablissement_id\": \"6\", \"justificatif_path\": \"dignitaires/diplomes/KZuLC4JzVK2CLOrMuc4gJGiTtk7m7gLC7URH4LbG.pdf\"}', '2026-07-10 00:29:04'),
(32, 'App\\Models\\User', 11, 'admin1', 'updated', 'Diplome', 6, 'Diplome Test Verif Modifie', '{\"id\": 6, \"code\": null, \"type\": \"Master\", \"annee\": null, \"intitule\": \"Diplome Test Verif\", \"ville_id\": null, \"domaine_id\": null, \"dignitaire_id\": 1, \"etablissement_id\": 6, \"justificatif_path\": \"dignitaires/diplomes/KZuLC4JzVK2CLOrMuc4gJGiTtk7m7gLC7URH4LbG.pdf\"}', '{\"type\": \"Doctorat\", \"intitule\": \"Diplome Test Verif Modifie\", \"dignitaire_id\": 1}', '2026-07-10 00:29:20'),
(33, 'App\\Models\\User', 11, 'admin1', 'deleted', 'Diplome', 6, 'Diplome Test Verif Modifie', '{\"id\": 6, \"code\": null, \"type\": \"Doctorat\", \"annee\": null, \"intitule\": \"Diplome Test Verif Modifie\", \"ville_id\": null, \"domaine_id\": null, \"dignitaire_id\": 1, \"etablissement_id\": 6, \"justificatif_path\": \"dignitaires/diplomes/KZuLC4JzVK2CLOrMuc4gJGiTtk7m7gLC7URH4LbG.pdf\"}', NULL, '2026-07-10 00:29:51'),
(34, 'App\\Models\\User', 11, 'admin1', 'updated', 'Dignitaire', 1, 'Ali BONGO ', '{\"photo\": \"image1.png\"}', '{\"photo\": \"MAT001_1783647133.jpg\"}', '2026-07-10 00:32:13'),
(35, 'App\\Models\\User', 11, 'admin1', 'created', 'Conjoint', 1, 'Test VerifConjointGlobal', NULL, '{\"nom\": \"VerifConjointGlobal\", \"genre\": \"F\", \"prenom\": \"Test\", \"statut\": \"actif\", \"dignitaire_id\": 1, \"est_militaire\": false, \"est_dignitaire\": false}', '2026-07-10 00:38:02'),
(36, 'App\\Models\\User', 11, 'admin1', 'deleted', 'Conjoint', 1, 'Test VerifConjointGlobal', '{\"id\": 1, \"nom\": \"VerifConjointGlobal\", \"email\": null, \"genre\": \"F\", \"photo\": null, \"prenom\": \"Test\", \"statut\": \"actif\", \"adresse\": null, \"employeur\": null, \"telephone\": null, \"created_at\": \"2026-07-10T01:38:02.000000Z\", \"profession\": null, \"updated_at\": \"2026-07-10T01:38:02.000000Z\", \"date_mariage\": null, \"lieu_mariage\": null, \"dignitaire_id\": 1, \"est_militaire\": false, \"date_fin_union\": null, \"date_naissance\": null, \"est_dignitaire\": false, \"nationalite_id\": null, \"grade_militaire\": null, \"acte_mariage_path\": null, \"lieu_naissance_id\": null, \"fonction_dignitaire\": null}', NULL, '2026-07-10 01:44:38'),
(37, 'App\\Models\\User', 11, 'admin1', 'created', 'Nomination', 6, 'Vice-President Test', NULL, '{\"statut\": \"en_cours\", \"fonction\": \"Vice-President Test\", \"entite_id\": \"6\", \"date_debut\": \"2026-07-10\", \"dignitaire_id\": \"1\", \"type_nomination\": \"nomination_officielle\", \"autorite_nominatrice\": \"Assemblee Generale FEG\", \"document_nomination_path\": \"dignitaires/nominations/VPL85COoejsdjXMuk3fGxrAm3cBchNLdj2tRE4af.png\"}', '2026-07-10 09:56:04'),
(38, 'App\\Models\\User', 11, 'admin1', 'updated', 'Nomination', 6, 'Vice-President Test Update', '{\"id\": 6, \"pv_id\": null, \"statut\": \"en_cours\", \"date_fin\": null, \"fonction\": \"Vice-President Test\", \"poste_id\": null, \"entite_id\": 6, \"motif_fin\": null, \"date_debut\": \"2026-07-10\", \"dignitaire_id\": 1, \"numero_decret\": null, \"rappel_envoye\": 0, \"type_nomination\": \"nomination_officielle\", \"autorite_nominatrice\": \"Assemblee Generale FEG\", \"document_nomination_path\": \"dignitaires/nominations/VPL85COoejsdjXMuk3fGxrAm3cBchNLdj2tRE4af.png\"}', '{\"fonction\": \"Vice-President Test Update\", \"entite_id\": \"6\", \"date_debut\": \"2026-07-10\", \"dignitaire_id\": \"1\", \"type_nomination\": \"nomination_officielle\", \"autorite_nominatrice\": \"Assemblee Generale FEG\", \"document_nomination_path\": \"dignitaires/nominations/PRYdZJ0zjY702QwlDTGNBi70Y1w62PU8HLweANOK.png\"}', '2026-07-10 09:57:46'),
(39, 'App\\Models\\User', 11, 'admin1', 'created', 'Entite', 8, 'Entite Test Verif', NULL, '{\"nom\": \"Entite Test Verif\", \"logo\": \"entites/logos/T0p6GzOIDoAPT1EIw0TeMRq1yqBw5QhKplut3Lnt.png\", \"email\": \"test@verif.ga\", \"telephone\": \"0102030405\"}', '2026-07-10 10:14:01'),
(40, 'App\\Models\\User', 11, 'admin1', 'deleted', 'Nomination', 6, 'Vice-President Test Update', '{\"id\": 6, \"pv_id\": null, \"statut\": \"en_cours\", \"date_fin\": null, \"fonction\": \"Vice-President Test Update\", \"poste_id\": null, \"entite_id\": 6, \"motif_fin\": null, \"date_debut\": \"2026-07-10\", \"dignitaire_id\": 1, \"numero_decret\": null, \"rappel_envoye\": 0, \"type_nomination\": \"nomination_officielle\", \"autorite_nominatrice\": \"Assemblee Generale FEG\", \"document_nomination_path\": \"dignitaires/nominations/PRYdZJ0zjY702QwlDTGNBi70Y1w62PU8HLweANOK.png\"}', NULL, '2026-07-10 10:14:38'),
(41, 'App\\Models\\User', 11, 'admin1', 'deleted', 'Entite', 8, 'Entite Test Verif', '{\"id\": 8, \"nom\": \"Entite Test Verif\", \"logo\": \"entites/logos/T0p6GzOIDoAPT1EIw0TeMRq1yqBw5QhKplut3Lnt.png\", \"type\": null, \"email\": \"test@verif.ga\", \"id_sup\": null, \"adresse\": null, \"site_web\": null, \"telephone\": \"0102030405\", \"description\": null, \"entite_rattachement_id\": null}', NULL, '2026-07-10 10:15:54'),
(42, 'App\\Models\\User', 11, 'admin1', 'created', 'CandidatMessage', 1, 'Test AuditCandidat', NULL, '{\"contenu\": \"Merci de completer votre CV avant la fin du mois.\"}', '2026-07-10 10:25:04'),
(43, 'App\\Models\\User', 11, 'admin1', 'created', 'CandidatMessage', 1, 'christian georges', NULL, '{\"contenu\": \"veuillez rentrer les diplomes et les langes\"}', '2026-07-10 10:28:08'),
(44, 'App\\Models\\User', 11, 'admin1', 'created', 'Affectation', 1, NULL, NULL, '{\"statut\": \"en_cours\", \"pays_id\": 2, \"date_debut\": \"2020-01-15\", \"dignitaire_id\": 1, \"type_affectation\": \"Ambassade\"}', '2026-07-20 15:13:06'),
(45, 'App\\Models\\User', 11, 'admin1', 'updated', 'Dignitaire', 1, 'Ali BONGO', '{\"id\": 1, \"nip\": \"NIP001\", \"nom\": \"BONGO \", \"genre\": \"Homme\", \"photo\": \"image1.png\", \"prenom\": \"Ali\", \"statut\": \"actif\", \"adresse\": null, \"casierJud\": null, \"matricule\": \"MAT001\", \"telephone\": null, \"etat_civil\": \"Marié\", \"nationalite\": null, \"certificatsMed\": null, \"date_naissance\": \"1959-02-09T00:00:00.000000Z\", \"lieu_naissance\": 1, \"nationalite_id\": null, \"date_prise_fonction\": null}', '{\"nip\": \"NIP001\", \"nom\": \"BONGO\", \"prenom\": \"Ali\", \"matricule\": \"MAT001\", \"nationalite_id\": 1}', '2026-07-20 15:14:37'),
(46, 'App\\Models\\User', 11, 'admin1', 'deleted', 'Affectation', 1, NULL, '{\"id\": 1, \"statut\": \"en_cours\", \"pays_id\": 2, \"date_fin\": null, \"ville_id\": null, \"created_at\": \"2026-07-20 16:13:06\", \"date_debut\": \"2020-01-15\", \"updated_at\": \"2026-07-20 16:13:06\", \"dignitaire_id\": 1, \"type_affectation\": \"Ambassade\"}', NULL, '2026-07-20 15:15:05'),
(47, 'App\\Models\\User', 11, 'admin1', 'updated', 'Dignitaire', 1, 'Ali BONGO', '{\"id\": 1, \"nip\": \"NIP001\", \"nom\": \"BONGO\", \"genre\": \"Homme\", \"photo\": \"image1.png\", \"prenom\": \"Ali\", \"statut\": \"actif\", \"adresse\": null, \"casierJud\": null, \"matricule\": \"MAT001\", \"telephone\": null, \"etat_civil\": \"Marié\", \"nationalite\": null, \"certificatsMed\": null, \"date_naissance\": \"1959-02-09T00:00:00.000000Z\", \"lieu_naissance\": 1, \"nationalite_id\": 1, \"date_prise_fonction\": null}', '{\"nip\": \"NIP001\", \"nom\": \"BONGO\", \"prenom\": \"Ali\", \"matricule\": \"MAT001\", \"nationalite_id\": null}', '2026-07-20 15:15:08'),
(48, 'App\\Models\\User', 11, 'admin1', 'updated', 'Parametres', NULL, 'Réglages OTP', NULL, '{\"otp_login_admin_enabled\": false, \"otp_login_candidat_enabled\": true}', '2026-07-20 21:05:39'),
(49, 'App\\Models\\User', 11, 'admin1', 'updated', 'Parametres', NULL, 'Réglages OTP', NULL, '{\"otp_login_admin_enabled\": true, \"otp_login_candidat_enabled\": false}', '2026-07-20 22:29:00'),
(50, 'App\\Models\\User', 11, 'admin1', 'updated', 'Parametres', NULL, 'Réglages OTP', NULL, '{\"otp_login_admin_enabled\": false, \"otp_login_candidat_enabled\": false}', '2026-07-20 23:03:09'),
(51, 'App\\Models\\User', 11, 'admin1', 'updated', 'Parametres', NULL, 'Réglages OTP', NULL, '{\"otp_login_admin_enabled\": true, \"otp_login_candidat_enabled\": false}', '2026-07-22 12:50:59'),
(52, 'App\\Models\\User', 11, 'admin1', 'updated', 'Parametres', NULL, 'Réglages OTP', NULL, '{\"otp_login_admin_enabled\": false, \"otp_login_candidat_enabled\": false}', '2026-07-22 12:54:41'),
(53, 'App\\Models\\User', 11, 'admin1', 'updated', 'Parametres', NULL, 'Réglages OTP', NULL, '{\"otp_login_admin_enabled\": true, \"otp_login_candidat_enabled\": false}', '2026-07-22 13:16:30'),
(54, 'App\\Models\\User', 11, 'admin1', 'updated', 'User', 11, 'admin1', '{\"email\": \"georgesrapontchombo22@gmail.com\", \"role_id\": 1, \"username\": \"admin1\", \"nom_complet\": \"admin1\"}', '{\"email\": \"georgesrapontchombo22@gmail.com\", \"role_id\": 1, \"username\": \"admin1\", \"fonctions\": [2, 5, 1, 4, 7, 3, 6], \"nom_complet\": \"admin1\", \"sousfonctions\": [{\"id\": 10, \"niveau\": \"lecture\"}, {\"id\": 2, \"niveau\": \"lecture\"}, {\"id\": 4, \"niveau\": \"lecture\"}, {\"id\": 1, \"niveau\": \"lecture\"}, {\"id\": 5, \"niveau\": \"lecture\"}, {\"id\": 6, \"niveau\": \"lecture\"}, {\"id\": 9, \"niveau\": \"lecture\"}, {\"id\": 7, \"niveau\": \"lecture\"}, {\"id\": 3, \"niveau\": \"lecture\"}, {\"id\": 12, \"niveau\": \"lecture\"}, {\"id\": 8, \"niveau\": \"lecture\"}, {\"id\": 11, \"niveau\": \"lecture\"}]}', '2026-07-22 13:51:00'),
(55, 'App\\Models\\User', 17, 'smoketest_poste_sync', 'created', 'Poste', 16, 'Ambassadeur du Gabon TEST_SYNC', NULL, '{\"statut\": \"en_cours\", \"intitule\": \"Ambassadeur du Gabon TEST_SYNC\", \"ville_id\": 1, \"date_debut\": \"2026-01-15\", \"dignitaire_id\": 1}', '2026-07-22 14:46:04'),
(56, 'App\\Models\\User', 17, 'smoketest_poste_sync', 'cloturee', 'Poste', 16, 'Ambassadeur du Gabon TEST_SYNC', '{\"id\": 16, \"statut\": \"en_cours\", \"date_fin\": null, \"intitule\": \"Ambassadeur du Gabon TEST_SYNC\", \"ville_id\": 1, \"entite_id\": null, \"motif_fin\": null, \"date_debut\": \"2026-01-15\", \"dignitaire_id\": 1}', '{\"statut\": \"terminee\", \"date_fin\": \"2026-06-01\", \"motif_fin\": \"fin_fonction\"}', '2026-07-22 14:46:46'),
(57, 'App\\Models\\User', 17, 'smoketest_poste_sync', 'deleted', 'Poste', 16, 'Ambassadeur du Gabon TEST_SYNC', '{\"id\": 16, \"statut\": \"terminee\", \"date_fin\": \"2026-06-01\", \"intitule\": \"Ambassadeur du Gabon TEST_SYNC\", \"ville_id\": 1, \"entite_id\": null, \"motif_fin\": \"fin_fonction\", \"date_debut\": \"2026-01-15\", \"dignitaire_id\": 1}', NULL, '2026-07-22 14:47:47'),
(58, 'App\\Models\\User', 18, 'smoketest_multi_affect', 'created', 'Poste', 17, 'Ambassadeur du Gabon TEST_MULTI', NULL, '{\"statut\": \"en_cours\", \"intitule\": \"Ambassadeur du Gabon TEST_MULTI\", \"ville_id\": 1, \"date_debut\": \"2026-01-15\", \"dignitaire_id\": 1}', '2026-07-22 14:59:54'),
(59, 'App\\Models\\User', 18, 'smoketest_multi_affect', 'created', 'Affectation', 3, NULL, NULL, '{\"statut\": \"en_cours\", \"pays_id\": 2, \"date_debut\": \"2026-03-01\", \"dignitaire_id\": 1, \"type_affectation\": \"Mission temporaire TEST\"}', '2026-07-22 15:00:00'),
(60, 'App\\Models\\User', 18, 'smoketest_multi_affect', 'deleted', 'Poste', 17, 'Ambassadeur du Gabon TEST_MULTI', '{\"id\": 17, \"statut\": \"en_cours\", \"date_fin\": null, \"intitule\": \"Ambassadeur du Gabon TEST_MULTI\", \"ville_id\": 1, \"entite_id\": null, \"motif_fin\": null, \"date_debut\": \"2026-01-15\", \"dignitaire_id\": 1}', NULL, '2026-07-22 15:02:09'),
(61, 'App\\Models\\User', 18, 'smoketest_multi_affect', 'deleted', 'Affectation', 3, NULL, '{\"id\": 3, \"statut\": \"en_cours\", \"pays_id\": 2, \"date_fin\": null, \"poste_id\": null, \"ville_id\": null, \"created_at\": \"2026-07-22 16:00:00\", \"date_debut\": \"2026-03-01\", \"updated_at\": \"2026-07-22 16:00:00\", \"dignitaire_id\": 1, \"type_affectation\": \"Mission temporaire TEST\"}', NULL, '2026-07-22 15:02:11'),
(62, 'App\\Models\\User', 19, 'smoketest_nature', 'created', 'Poste', 18, 'Ambassadeur TEST_NATURE', NULL, '{\"statut\": \"en_cours\", \"intitule\": \"Ambassadeur TEST_NATURE\", \"ville_id\": 1, \"date_debut\": \"2026-01-15\", \"dignitaire_id\": 1}', '2026-07-22 15:12:01'),
(63, 'App\\Models\\User', 19, 'smoketest_nature', 'created', 'Affectation', 5, NULL, NULL, '{\"nature\": \"principale\", \"statut\": \"en_cours\", \"pays_id\": 2, \"date_debut\": \"2026-03-01\", \"dignitaire_id\": 1}', '2026-07-22 15:12:05'),
(64, 'App\\Models\\User', 19, 'smoketest_nature', 'created', 'Affectation', 6, NULL, NULL, '{\"nature\": \"mission_temporaire\", \"statut\": \"en_cours\", \"pays_id\": 3, \"date_debut\": \"2026-04-01\", \"dignitaire_id\": 1, \"type_affectation\": \"Mission\"}', '2026-07-22 15:12:07'),
(65, 'App\\Models\\User', 19, 'smoketest_nature', 'deleted', 'Poste', 18, 'Ambassadeur TEST_NATURE', '{\"id\": 18, \"statut\": \"en_cours\", \"date_fin\": null, \"intitule\": \"Ambassadeur TEST_NATURE\", \"ville_id\": 1, \"entite_id\": null, \"motif_fin\": null, \"date_debut\": \"2026-01-15\", \"dignitaire_id\": 1}', NULL, '2026-07-22 15:12:53'),
(66, 'App\\Models\\User', 19, 'smoketest_nature', 'deleted', 'Affectation', 5, NULL, '{\"id\": 5, \"nature\": \"principale\", \"statut\": \"en_cours\", \"pays_id\": 2, \"date_fin\": null, \"poste_id\": null, \"ville_id\": null, \"created_at\": \"2026-07-22 16:12:05\", \"date_debut\": \"2026-03-01\", \"updated_at\": \"2026-07-22 16:12:05\", \"dignitaire_id\": 1, \"type_affectation\": null}', NULL, '2026-07-22 15:12:57'),
(67, 'App\\Models\\User', 19, 'smoketest_nature', 'deleted', 'Affectation', 6, NULL, '{\"id\": 6, \"nature\": \"mission_temporaire\", \"statut\": \"en_cours\", \"pays_id\": 3, \"date_fin\": null, \"poste_id\": null, \"ville_id\": null, \"created_at\": \"2026-07-22 16:12:07\", \"date_debut\": \"2026-04-01\", \"updated_at\": \"2026-07-22 16:12:07\", \"dignitaire_id\": 1, \"type_affectation\": \"Mission\"}', NULL, '2026-07-22 15:13:02'),
(68, 'App\\Models\\User', 23, 'smoketest_dates', 'created', 'Poste', 19, 'TEST_DATES', NULL, '{\"statut\": \"en_cours\", \"date_fin\": \"2026-01-16\", \"intitule\": \"TEST_DATES\", \"date_debut\": \"2026-01-15\", \"dignitaire_id\": 1}', '2026-07-22 19:03:39'),
(69, 'App\\Models\\User', 23, 'smoketest_dates', 'created', 'Conjoint', 1, 'Conjoint TEST', NULL, '{\"nom\": \"TEST\", \"genre\": \"F\", \"prenom\": \"Conjoint\", \"statut\": \"actif\", \"date_mariage\": \"2020-01-15\", \"dignitaire_id\": 1, \"est_militaire\": false, \"est_dignitaire\": false}', '2026-07-22 19:08:20'),
(70, 'App\\Models\\User', 23, 'smoketest_dates', 'updated', 'Conjoint', 1, 'Conjoint TEST', '{\"id\": 1, \"nom\": \"TEST\", \"email\": null, \"genre\": \"F\", \"photo\": null, \"prenom\": \"Conjoint\", \"statut\": \"actif\", \"adresse\": null, \"employeur\": null, \"telephone\": null, \"created_at\": \"2026-07-22T20:08:20.000000Z\", \"profession\": null, \"updated_at\": \"2026-07-22T20:08:20.000000Z\", \"date_mariage\": \"2020-01-15T00:00:00.000000Z\", \"lieu_mariage\": null, \"dignitaire_id\": 1, \"est_militaire\": false, \"date_fin_union\": null, \"date_naissance\": null, \"est_dignitaire\": false, \"nationalite_id\": null, \"grade_militaire\": null, \"acte_mariage_path\": null, \"lieu_naissance_id\": null, \"fonction_dignitaire\": null}', '{\"statut\": \"divorce\", \"date_fin_union\": \"2021-06-01\"}', '2026-07-22 19:08:27'),
(71, 'App\\Models\\User', 23, 'smoketest_dates', 'deleted', 'Poste', 19, 'TEST_DATES', '{\"id\": 19, \"statut\": \"en_cours\", \"date_fin\": \"2026-01-16\", \"intitule\": \"TEST_DATES\", \"ville_id\": null, \"entite_id\": null, \"motif_fin\": null, \"date_debut\": \"2026-01-15\", \"dignitaire_id\": 1}', NULL, '2026-07-22 19:10:11'),
(72, 'App\\Models\\User', 23, 'smoketest_dates', 'deleted', 'Conjoint', 1, 'Conjoint TEST', '{\"id\": 1, \"nom\": \"TEST\", \"email\": null, \"genre\": \"F\", \"photo\": null, \"prenom\": \"Conjoint\", \"statut\": \"divorce\", \"adresse\": null, \"employeur\": null, \"telephone\": null, \"created_at\": \"2026-07-22T20:08:20.000000Z\", \"profession\": null, \"updated_at\": \"2026-07-22T20:08:27.000000Z\", \"date_mariage\": \"2020-01-15T00:00:00.000000Z\", \"lieu_mariage\": null, \"dignitaire_id\": 1, \"est_militaire\": false, \"date_fin_union\": \"2021-06-01T00:00:00.000000Z\", \"date_naissance\": null, \"est_dignitaire\": false, \"nationalite_id\": null, \"grade_militaire\": null, \"acte_mariage_path\": null, \"lieu_naissance_id\": null, \"fonction_dignitaire\": null}', NULL, '2026-07-22 19:10:16'),
(73, 'App\\Models\\User', 24, 'smoketest_ville', 'created', 'Affectation', 7, NULL, NULL, '{\"nature\": \"principale\", \"statut\": \"en_cours\", \"pays_id\": 40, \"ville_id\": 587, \"date_debut\": \"2026-01-15\", \"dignitaire_id\": 1}', '2026-07-22 19:42:27'),
(74, 'App\\Models\\User', 24, 'smoketest_ville', 'created', 'Affectation', 8, NULL, NULL, '{\"nature\": \"mission_temporaire\", \"statut\": \"en_cours\", \"pays_id\": 40, \"ville_id\": 587, \"date_debut\": \"2026-02-01\", \"dignitaire_id\": 1}', '2026-07-22 19:43:13'),
(75, 'App\\Models\\User', 24, 'smoketest_ville', 'created', 'Conjoint', 2, 'Conjoint2 TEST', NULL, '{\"nom\": \"TEST\", \"genre\": \"F\", \"prenom\": \"Conjoint2\", \"statut\": \"actif\", \"dignitaire_id\": 1, \"est_militaire\": false, \"est_dignitaire\": false, \"nationalite_id\": 40, \"lieu_naissance_id\": 588}', '2026-07-22 19:43:19'),
(76, 'App\\Models\\User', 24, 'smoketest_ville', 'deleted', 'Affectation', 7, NULL, '{\"id\": 7, \"nature\": \"principale\", \"statut\": \"en_cours\", \"pays_id\": 40, \"date_fin\": null, \"poste_id\": null, \"ville_id\": 587, \"created_at\": \"2026-07-22 20:42:27\", \"date_debut\": \"2026-01-15\", \"updated_at\": \"2026-07-22 20:42:27\", \"dignitaire_id\": 1, \"type_affectation\": null}', NULL, '2026-07-22 19:45:06'),
(77, 'App\\Models\\User', 24, 'smoketest_ville', 'deleted', 'Affectation', 8, NULL, '{\"id\": 8, \"nature\": \"mission_temporaire\", \"statut\": \"en_cours\", \"pays_id\": 40, \"date_fin\": null, \"poste_id\": null, \"ville_id\": 587, \"created_at\": \"2026-07-22 20:43:13\", \"date_debut\": \"2026-02-01\", \"updated_at\": \"2026-07-22 20:43:13\", \"dignitaire_id\": 1, \"type_affectation\": null}', NULL, '2026-07-22 19:45:13'),
(78, 'App\\Models\\User', 24, 'smoketest_ville', 'deleted', 'Conjoint', 2, 'Conjoint2 TEST', '{\"id\": 2, \"nom\": \"TEST\", \"email\": null, \"genre\": \"F\", \"photo\": null, \"prenom\": \"Conjoint2\", \"statut\": \"actif\", \"adresse\": null, \"employeur\": null, \"telephone\": null, \"created_at\": \"2026-07-22T20:43:19.000000Z\", \"profession\": null, \"updated_at\": \"2026-07-22T20:43:19.000000Z\", \"date_mariage\": null, \"lieu_mariage\": null, \"dignitaire_id\": 1, \"est_militaire\": false, \"date_fin_union\": null, \"date_naissance\": null, \"est_dignitaire\": false, \"nationalite_id\": 40, \"grade_militaire\": null, \"acte_mariage_path\": null, \"lieu_naissance_id\": 588, \"fonction_dignitaire\": null}', NULL, '2026-07-22 19:45:19'),
(79, 'App\\Models\\User', 29, 'smoketest_secu', 'created', 'Entite', 8, 'TEST_ENTITE_PERM', NULL, '{\"nom\": \"TEST_ENTITE_PERM\"}', '2026-07-22 23:38:23'),
(80, 'App\\Models\\Candidat', 4, 'christian georges', 'connexion', 'Session', 4, 'christian georges', NULL, '{\"ip\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0\"}', '2026-08-04 12:05:07'),
(81, 'App\\Models\\User', 11, 'admin1', 'connexion', 'Session', 11, 'admin1', NULL, '{\"ip\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0\"}', '2026-08-04 12:16:06'),
(82, 'App\\Models\\User', 11, 'admin1', 'created', 'CandidatMessage', 2, 'christian georges', NULL, '{\"contenu\": \"veuillez téléverser vos document avant le 10/08/2026\"}', '2026-08-04 12:20:21'),
(83, 'App\\Models\\Candidat', 4, 'christian georges', 'connexion', 'Session', 4, 'christian georges', NULL, '{\"ip\": \"127.0.0.1\", \"user_agent\": \"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0\"}', '2026-08-04 12:21:09'),
(84, 'App\\Models\\User', 11, 'admin1', 'created', 'Affectation', 1, NULL, NULL, '{\"nature\": \"principale\", \"statut\": \"en_cours\", \"pays_id\": 2, \"date_fin\": null, \"ville_id\": 2, \"date_debut\": \"2026-04-16\", \"dignitaire_id\": 1, \"type_affectation\": \"Autre\"}', '2026-08-04 12:29:42'),
(85, 'App\\Models\\User', 11, 'admin1', 'created', 'Conjoint', 1, 'christian georges', NULL, '{\"nom\": \"georges\", \"email\": \"georgesrapontchombo@gmail.com\", \"genre\": \"F\", \"prenom\": \"christian\", \"statut\": \"actif\", \"adresse\": \"georgesrapontchombo@gmail.com\", \"employeur\": null, \"telephone\": \"07453628\", \"profession\": null, \"date_mariage\": null, \"lieu_mariage\": null, \"dignitaire_id\": 12, \"est_militaire\": false, \"date_naissance\": \"1998-08-14\", \"est_dignitaire\": false, \"nationalite_id\": 8, \"grade_militaire\": null, \"lieu_naissance_id\": 130, \"fonction_dignitaire\": null}', '2026-08-04 12:41:27');

-- --------------------------------------------------------

--
-- Structure de la table `candidats`
--

CREATE TABLE `candidats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `statut` enum('en_attente','valide','refuse') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `motif_refus` text COLLATE utf8mb4_unicode_ci COMMENT 'Raison du refus si statut = refuse',
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date NOT NULL,
  `genre` enum('M','F') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `matricule` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lieu_naissance_id` int(11) DEFAULT NULL,
  `etat_civil` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lettre_motivation_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verifie_le` timestamp NULL DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ville_residence_id` int(11) DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mot de passe haché pour connexion candidat',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_candidature` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `valide_par` int(11) DEFAULT NULL COMMENT 'Admin qui a validé/refusé la candidature',
  `date_validation` timestamp NULL DEFAULT NULL,
  `dignitaire_id` int(11) DEFAULT NULL COMMENT 'Lien vers le dignitaire créé après validation',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidats`
--

INSERT INTO `candidats` (`id`, `statut`, `motif_refus`, `nom`, `prenom`, `date_naissance`, `genre`, `nip`, `matricule`, `lieu_naissance_id`, `etat_civil`, `photo`, `cv_path`, `lettre_motivation_path`, `email`, `email_verifie_le`, `telephone`, `adresse`, `ville_residence_id`, `password`, `remember_token`, `date_candidature`, `valide_par`, `date_validation`, `dignitaire_id`, `created_at`, `updated_at`) VALUES
(2, 'en_attente', NULL, 'georges', 'christian', '1990-01-01', 'M', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'verif.audit.1782944451@example.com', NULL, '074 12 04 08', NULL, NULL, '$2y$10$QcLjhCUzhgx9R7r6n/EYJuNatQVpSDGJPadEO5ZLDR7O07w1F7rLW', NULL, '2026-07-01 22:20:52', NULL, NULL, NULL, '2026-07-01 21:20:52', '2026-07-20 17:04:51'),
(3, 'en_attente', NULL, 'georges', 'christian', '2000-04-22', 'M', '5446545', '8415641999', 49, 'Marié(e)', 'candidats/photos/6a4952850d165_1783190149.png', NULL, NULL, 'georges@gmail.com', NULL, '074 12 04 08', NULL, NULL, '$2y$10$niQgBxupmCGJmEatv4tM8OZTgB0VG0DqhjHIHKTfjEF8CsTWgma26', NULL, '2026-07-04 18:35:51', NULL, NULL, NULL, '2026-07-04 17:35:51', '2026-07-04 17:35:51'),
(4, 'en_attente', NULL, 'georges', 'christian', '1999-04-22', 'M', '159195', NULL, 59, 'Célibataire', 'candidats/photos/6a71e56c4b587_1785849196.png', NULL, NULL, 'georgesrapontchombo@gmail.com', '2026-08-04 12:05:03', '07453628', NULL, NULL, '$2y$10$pyA7gwrBt/OfF8fHrFB2S.SfmYXMa5KEI3uEb2rEajI18Xbv4FDfe', NULL, '2026-08-04 13:03:24', NULL, NULL, NULL, '2026-08-04 12:03:24', '2026-08-04 12:13:19');

-- --------------------------------------------------------

--
-- Structure de la table `candidat_diplomes`
--

CREATE TABLE `candidat_diplomes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `candidat_id` bigint(20) UNSIGNED NOT NULL,
  `intitule` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `etablissement_id` int(11) DEFAULT NULL,
  `ville_id` int(11) DEFAULT NULL,
  `domaine_id` int(11) DEFAULT NULL,
  `annee` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `justificatif_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valide_par` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID de l''admin qui a validé/rejeté',
  `valide_le` timestamp NULL DEFAULT NULL,
  `motif_rejet` text COLLATE utf8mb4_unicode_ci,
  `statut_validation` enum('en_attente','valide','rejete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidat_diplomes`
--

INSERT INTO `candidat_diplomes` (`id`, `candidat_id`, `intitule`, `type`, `etablissement_id`, `ville_id`, `domaine_id`, `annee`, `justificatif_path`, `valide_par`, `valide_le`, `motif_rejet`, `statut_validation`, `created_at`, `updated_at`) VALUES
(1, 4, 'bepc', 'BEPC', 3, NULL, 4, '1999', 'candidats/diplomes/oBwWEntPFWrDnyHbG1LxhyuSAY50KzRCbSs5v8UF.pdf', NULL, NULL, NULL, 'en_attente', '2026-08-05 10:28:55', '2026-08-05 10:28:55');

-- --------------------------------------------------------

--
-- Structure de la table `candidat_documents`
--

CREATE TABLE `candidat_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `candidat_id` bigint(20) UNSIGNED NOT NULL,
  `type_document` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'diplome, attestation, casier, medical, cv, lettre, autre',
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nom original du fichier',
  `chemin_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Chemin de stockage du fichier',
  `taille_fichier` int(11) DEFAULT NULL COMMENT 'Taille en octets',
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Extension du fichier (pdf, jpg, png, etc.)',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Description optionnelle du document',
  `statut_validation` enum('en_attente','valide','rejete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `motif_rejet` text COLLATE utf8mb4_unicode_ci,
  `valide_le` timestamp NULL DEFAULT NULL,
  `valide_par` bigint(20) UNSIGNED DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidat_documents`
--

INSERT INTO `candidat_documents` (`id`, `candidat_id`, `type_document`, `nom_fichier`, `chemin_fichier`, `taille_fichier`, `extension`, `description`, `statut_validation`, `motif_rejet`, `valide_le`, `valide_par`, `uploaded_at`, `created_at`, `updated_at`) VALUES
(1, 4, 'cv', 'Compréhension des objectifs spécifique.pdf', 'candidats/documents/9KycIv2HZFy1Z4t8mOeYUTlONwEV3WSFk4QOblyg.pdf', 150370, 'pdf', NULL, 'valide', NULL, '2026-08-05 12:36:32', 0, '2026-08-05 13:32:48', '2026-08-05 12:32:48', '2026-08-05 12:36:32'),
(2, 4, 'cv', 'Compréhension des objectifs spécifique.pdf', 'candidats/documents/cFRDRqhbClFDPBpTZAwSXbZNk5pNz7QQbQz2sIM2.pdf', 150370, 'pdf', NULL, 'en_attente', NULL, NULL, NULL, '2026-08-05 13:33:41', '2026-08-05 12:33:41', '2026-08-05 12:33:41');

-- --------------------------------------------------------

--
-- Structure de la table `candidat_experiences`
--

CREATE TABLE `candidat_experiences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `candidat_id` bigint(20) UNSIGNED NOT NULL,
  `intitule` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `structure_id` int(11) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `justificatif_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valide_par` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ID de l''admin qui a validé/rejeté',
  `valide_le` timestamp NULL DEFAULT NULL,
  `motif_rejet` text COLLATE utf8mb4_unicode_ci,
  `statut_validation` enum('en_attente','valide','rejete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en_attente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidat_experiences`
--

INSERT INTO `candidat_experiences` (`id`, `candidat_id`, `intitule`, `structure_id`, `date_debut`, `date_fin`, `justificatif_path`, `valide_par`, `valide_le`, `motif_rejet`, `statut_validation`, `created_at`, `updated_at`) VALUES
(1, 4, 'genie informatique', 3, '2026-08-03', NULL, 'candidats/experiences/tvx8RWlTqV7CHzouTGjOQ7BC5834JXoyddH336ik.pdf', NULL, NULL, NULL, 'en_attente', '2026-08-05 10:32:22', '2026-08-05 10:32:22');

-- --------------------------------------------------------

--
-- Structure de la table `candidat_langues`
--

CREATE TABLE `candidat_langues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `candidat_id` bigint(20) UNSIGNED NOT NULL,
  `langue_id` int(11) NOT NULL,
  `niveau` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidat_langues`
--

INSERT INTO `candidat_langues` (`id`, `candidat_id`, `langue_id`, `niveau`, `created_at`, `updated_at`) VALUES
(1, 4, 3, 'Intermédiaire', '2026-08-04 12:08:50', '2026-08-04 12:08:50');

-- --------------------------------------------------------

--
-- Structure de la table `candidat_messages`
--

CREATE TABLE `candidat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `candidat_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('recommandation','validation','refus') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recommandation',
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  `lu_le` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidat_messages`
--

INSERT INTO `candidat_messages` (`id`, `candidat_id`, `user_id`, `user_label`, `type`, `contenu`, `lu`, `lu_le`, `created_at`, `updated_at`) VALUES
(1, 3, 11, 'admin1', 'recommandation', 'veuillez rentrer les diplomes et les langes', 1, '2026-07-10 10:31:57', '2026-07-10 10:28:08', '2026-07-10 10:31:57'),
(2, 4, 11, 'admin1', 'recommandation', 'veuillez téléverser vos document avant le 10/08/2026', 1, '2026-08-04 12:22:04', '2026-08-04 12:20:21', '2026-08-04 12:22:04');

-- --------------------------------------------------------

--
-- Structure de la table `conjoints`
--

CREATE TABLE `conjoints` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `genre` enum('M','F') COLLATE utf8mb4_unicode_ci NOT NULL,
  `lieu_naissance_id` int(11) DEFAULT NULL,
  `nationalite_id` int(11) DEFAULT NULL,
  `profession` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employeur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_mariage` date DEFAULT NULL,
  `lieu_mariage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `statut` enum('actif','divorce','veuf','separe') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'actif',
  `date_fin_union` date DEFAULT NULL COMMENT 'Date du divorce, décès ou séparation',
  `est_militaire` tinyint(1) NOT NULL DEFAULT '0',
  `est_dignitaire` tinyint(1) NOT NULL DEFAULT '0',
  `grade_militaire` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fonction_dignitaire` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` text COLLATE utf8mb4_unicode_ci,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acte_mariage_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `conjoints`
--

INSERT INTO `conjoints` (`id`, `dignitaire_id`, `nom`, `prenom`, `date_naissance`, `genre`, `lieu_naissance_id`, `nationalite_id`, `profession`, `employeur`, `date_mariage`, `lieu_mariage`, `statut`, `date_fin_union`, `est_militaire`, `est_dignitaire`, `grade_militaire`, `fonction_dignitaire`, `telephone`, `email`, `adresse`, `photo`, `acte_mariage_path`, `created_at`, `updated_at`) VALUES
(1, 12, 'georges', 'christian', '1998-08-14', 'F', 130, 8, NULL, NULL, NULL, NULL, 'actif', NULL, 0, 0, NULL, NULL, '07453628', 'georgesrapontchombo@gmail.com', 'georgesrapontchombo@gmail.com', NULL, NULL, '2026-08-04 12:41:27', '2026-08-04 12:41:27');

-- --------------------------------------------------------

--
-- Structure de la table `decoration`
--

CREATE TABLE `decoration` (
  `deco_id` int(11) NOT NULL,
  `deco_nom` varchar(150) DEFAULT NULL,
  `deco_type` varchar(50) DEFAULT NULL,
  `deco_niveau` varchar(50) DEFAULT NULL,
  `deco_grade` varchar(50) DEFAULT NULL,
  `deco_date_obtention` date DEFAULT NULL,
  `deco_autorite` varchar(50) DEFAULT NULL,
  `deco_motif` varchar(50) DEFAULT NULL,
  `deco_description` varchar(255) DEFAULT NULL,
  `deco_fichierAttestation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `decoration`
--

INSERT INTO `decoration` (`deco_id`, `deco_nom`, `deco_type`, `deco_niveau`, `deco_grade`, `deco_date_obtention`, `deco_autorite`, `deco_motif`, `deco_description`, `deco_fichierAttestation`) VALUES
(1, 'Ordre National du Mérite', 'National', 'Or', 'Grand Officier', '2010-05-01', 'Président', 'Service rendu', 'Décoration nationale pour service exceptionnel', 'attestation1.pdf'),
(2, 'Médaille du Travail', 'Professionnel', 'Argent', 'Officier', '2015-09-15', 'Ministre du Travail', 'Ancienneté', 'Récompense pour 30 ans de service', 'attestation2.pdf');

-- --------------------------------------------------------

--
-- Structure de la table `decoration_dignitaire`
--

CREATE TABLE `decoration_dignitaire` (
  `id` int(11) NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `decoration_id` int(11) NOT NULL,
  `date_attribution` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `decoration_dignitaire`
--

INSERT INTO `decoration_dignitaire` (`id`, `dignitaire_id`, `decoration_id`, `date_attribution`) VALUES
(1, 1, 1, '2010-05-01'),
(2, 2, 2, '2015-09-15');

-- --------------------------------------------------------

--
-- Structure de la table `dignitaire`
--

CREATE TABLE `dignitaire` (
  `id` int(11) NOT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `date_prise_fonction` date DEFAULT NULL,
  `lieu_naissance` int(11) DEFAULT NULL,
  `nationalite` varchar(100) DEFAULT NULL,
  `nationalite_id` int(11) DEFAULT NULL,
  `genre` varchar(10) DEFAULT NULL,
  `etat_civil` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `casierJud` varchar(255) DEFAULT NULL,
  `certificatsMed` varchar(255) DEFAULT NULL,
  `statut` enum('actif','retraite','non_localise') NOT NULL DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `dignitaire`
--

INSERT INTO `dignitaire` (`id`, `nip`, `matricule`, `nom`, `prenom`, `date_naissance`, `date_prise_fonction`, `lieu_naissance`, `nationalite`, `nationalite_id`, `genre`, `etat_civil`, `photo`, `adresse`, `telephone`, `casierJud`, `certificatsMed`, `statut`) VALUES
(1, 'NIP001', 'MAT001', 'BONGO', 'Ali', '1959-02-09', NULL, 1, NULL, NULL, 'Homme', 'Marié', 'image1.png', NULL, NULL, NULL, NULL, 'actif'),
(2, 'NIP002', 'MAT002', 'ONDO', 'Rose', '1965-05-12', NULL, 2, NULL, NULL, 'Femme', 'Veuve', 'image2.png', NULL, NULL, NULL, NULL, 'actif'),
(3, 'NIP003', 'MAT003', 'NDONG', 'Paul', '1971-09-17', NULL, 3, NULL, NULL, 'Homme', 'Célibataire', 'image3.png', NULL, NULL, NULL, NULL, 'actif'),
(4, 'NIP004', 'MAT004', 'MOUSSA', 'Fatou', '1968-11-23', NULL, 4, NULL, NULL, 'Femme', 'Mariée', 'image1.png', NULL, NULL, NULL, NULL, 'actif'),
(5, 'NIP005', 'MAT005', 'MEYE ', 'Serge', '1975-03-30', NULL, 1, NULL, NULL, 'Homme', 'divorcé', 'image2.png', NULL, NULL, NULL, NULL, 'actif'),
(6, 'NIP006', 'MAT006', 'NDONG', 'Raymond', '1970-05-15', NULL, 1, 'Gabonaise', NULL, 'Homme', 'Marié(e)', 'image3.png', 'Libreville', '0712345678', NULL, NULL, 'actif'),
(7, 'NIP007', 'MAT007', 'BOUMBA', 'Clarisse', '1975-08-20', NULL, 2, 'Gabonaise', NULL, 'Femme', 'Célibataire', 'image1.png', 'Port-Gentil', '0623456789', NULL, NULL, 'actif'),
(8, 'NIP008', 'MAT008', 'MABICKA', 'Jean-Paul', '1965-03-10', NULL, 3, 'Gabonaise', NULL, 'Homme', 'Marié(e)', 'image2.png', 'Franceville', '0777777777', NULL, NULL, 'actif'),
(9, 'NIP009', 'MAT009', 'NTOUTOUME', 'Agnès', '1980-09-12', NULL, 4, 'Gabonaise', NULL, 'Femme', 'Marié(e)', 'image3.png', 'Lambaréné', '0611223344', NULL, NULL, 'actif'),
(10, 'NIP010', 'MAT0010', 'OKOME', 'Franck', '1982-11-23', NULL, 5, 'Gabonaise', NULL, 'Homme', 'Célibataire', 'image1.png', 'Oyem', '0666778899', NULL, NULL, 'actif'),
(11, 'NIP0011', 'MAT0011', 'MBOUMBA', 'Georgette', '1978-02-28', NULL, 1, 'Gabonaise', NULL, 'Femme', 'Veuve', 'image2.png', 'Mouila', '0755566666', NULL, NULL, 'actif'),
(12, 'NIP0012', 'MAT0012', 'BONGO', 'Albert', '1955-07-30', NULL, 2, 'Gabonaise', NULL, 'Homme', 'Marié(e)', 'image3.png', 'Libreville', '0600001122', NULL, NULL, 'actif'),
(13, 'NIP0013', 'MAT0013', 'MBINA', 'Rose', '1985-01-05', NULL, 3, 'Gabonaise', NULL, 'Femme', 'Célibataire', 'image1.png', 'Port-Gentil', '0688996655', NULL, NULL, 'actif'),
(14, 'NIP0014', 'MAT0014', 'KOUMBA', 'Richard', '1972-06-17', NULL, 4, 'Gabonaise', NULL, 'Homme', 'Divorcé(e)', 'image2.png', 'Franceville', '0701010101', NULL, NULL, 'actif'),
(15, 'NIP015', 'MAT015', 'NGUEMA', 'Sylvia', '1990-12-11', NULL, 5, 'Gabonaise', NULL, 'Femme', 'Célibataire', 'image1.png', 'Bitam', '0696969696', NULL, NULL, 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `dignitaire_documents`
--

CREATE TABLE `dignitaire_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `type_document` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_document` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_emission` date DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `organisme_emetteur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille_fichier` int(11) DEFAULT NULL,
  `extension` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dignitaire_emails`
--

CREATE TABLE `dignitaire_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `type` varchar(20) DEFAULT 'personnel',
  `principal` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `dignitaire_emails`
--

INSERT INTO `dignitaire_emails` (`id`, `dignitaire_id`, `email`, `type`, `principal`, `created_at`, `updated_at`) VALUES
(1, 1, 'ali.bongo.@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(2, 1, 'ali.bongo.@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(3, 2, 'rose.ondo@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(4, 2, 'rose.ondo@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(5, 3, 'paul.ndong@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(6, 3, 'paul.ndong@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(7, 4, 'fatou.moussa@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(8, 4, 'fatou.moussa@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(9, 5, 'serge.meye.@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(10, 5, 'serge.meye.@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(11, 6, 'raymond.ndong@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(12, 6, 'raymond.ndong@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(13, 7, 'clarisse.boumba@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(14, 7, 'clarisse.boumba@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(15, 8, 'jean-paul.mabicka@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(16, 8, 'jean-paul.mabicka@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(17, 9, 'agnès.ntoutoume@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(18, 9, 'agnès.ntoutoume@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(19, 10, 'franck.okome@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(20, 10, 'franck.okome@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(21, 11, 'georgette.mboumba@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(22, 11, 'georgette.mboumba@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(23, 12, 'albert.bongo@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(24, 12, 'albert.bongo@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(25, 13, 'rose.mbina@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(26, 13, 'rose.mbina@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(27, 14, 'richard.koumba@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(28, 14, 'richard.koumba@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(29, 15, 'sylvia.nguema@gouv.cm', 'professionnel', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(30, 15, 'sylvia.nguema@gmail.com', 'personnel', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16');

-- --------------------------------------------------------

--
-- Structure de la table `dignitaire_telephones`
--

CREATE TABLE `dignitaire_telephones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mobile',
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `dignitaire_telephones`
--

INSERT INTO `dignitaire_telephones` (`id`, `dignitaire_id`, `numero`, `type`, `principal`, `created_at`, `updated_at`) VALUES
(1, 1, '+237 670936653', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(2, 1, '+237 224426866', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(3, 2, '+237 676536256', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(4, 2, '+237 228858011', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(5, 3, '+237 673687493', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(6, 3, '+237 223502000', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(7, 4, '+237 675105830', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(8, 4, '+237 221120254', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(9, 5, '+237 676700033', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(10, 5, '+237 221951693', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(11, 6, '+237 675473409', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(12, 6, '+237 222487342', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(13, 7, '+237 677163702', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(14, 7, '+237 222076919', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(15, 8, '+237 677289825', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(16, 8, '+237 222109912', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(17, 9, '+237 673394970', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(18, 9, '+237 222532794', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(19, 10, '+237 679048137', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(20, 10, '+237 229621808', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(21, 11, '+237 679473419', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(22, 11, '+237 220719038', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(23, 12, '+237 675082551', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(24, 12, '+237 222404012', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(25, 13, '+237 677877477', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(26, 13, '+237 225394949', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(27, 14, '+237 673400095', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(28, 14, '+237 228350579', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(29, 15, '+237 671927600', 'mobile', 1, '2026-05-21 00:18:16', '2026-05-21 00:18:16'),
(30, 15, '+237 229887219', 'bureau', 0, '2026-05-21 00:18:16', '2026-05-21 00:18:16');

-- --------------------------------------------------------

--
-- Structure de la table `diplome`
--

CREATE TABLE `diplome` (
  `id` int(11) NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `intitule` varchar(255) DEFAULT NULL,
  `etablissement_id` int(11) DEFAULT NULL,
  `annee` varchar(10) DEFAULT NULL,
  `ville_id` int(11) DEFAULT NULL,
  `domaine_id` int(11) DEFAULT NULL,
  `code` varchar(30) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `justificatif_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `diplome`
--

INSERT INTO `diplome` (`id`, `dignitaire_id`, `intitule`, `etablissement_id`, `annee`, `ville_id`, `domaine_id`, `code`, `type`, `justificatif_path`) VALUES
(1, 1, 'Doctorat en Droit', 1, '1990', 1, 2, 'DOC001', 'Doctorat', NULL),
(2, 2, 'Master Sciences Politiques', 2, '1992', 2, 1, 'MAS001', 'Master', NULL),
(3, 3, 'Licence Informatique', 3, '1995', 3, 5, 'LIC001', 'Licence', NULL),
(4, 4, 'DESS Gestion', 4, '1993', 4, 7, 'DESS001', 'DESS', NULL),
(5, 5, 'CAPES Lettres Modernes', 5, '1991', 1, 6, 'CAP001', 'CAPES', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `domaine`
--

CREATE TABLE `domaine` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `domaine`
--

INSERT INTO `domaine` (`id`, `nom`, `description`) VALUES
(1, 'Sciences Politiques', NULL),
(2, 'Droit', NULL),
(3, 'Économie', NULL),
(4, 'Administration Publique', NULL),
(5, 'Informatique', NULL),
(6, 'Lettres Modernes', NULL),
(7, 'Gestion', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `enfants`
--

CREATE TABLE `enfants` (
  `id` int(11) NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` int(11) DEFAULT NULL,
  `genre` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `enfants`
--

INSERT INTO `enfants` (`id`, `dignitaire_id`, `nom`, `prenom`, `date_naissance`, `lieu_naissance`, `genre`) VALUES
(1, 1, 'BONGO', 'Junior', '1990-07-14', 1, 'Homme'),
(2, 1, 'BONGO', 'Sylvia', '1994-01-22', 2, 'Femme'),
(3, 2, 'ONDO', 'Patrick', '1989-05-12', 2, 'Homme'),
(4, 3, 'MOUSSA', 'Mariama', '2002-08-05', 4, 'Femme'),
(5, 4, 'MEYE', 'Nicolas', '2003-12-20', 1, 'Homme');

-- --------------------------------------------------------

--
-- Structure de la table `entite`
--

CREATE TABLE `entite` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `id_sup` int(11) DEFAULT NULL,
  `entite_rattachement_id` int(11) DEFAULT NULL,
  `description` text,
  `logo` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `adresse` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `entite`
--

INSERT INTO `entite` (`id`, `nom`, `type`, `id_sup`, `entite_rattachement_id`, `description`, `logo`, `telephone`, `email`, `site_web`, `adresse`) VALUES
(1, 'Présidence de la République', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Ministère de la Défense', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Ministère de l’Intérieur', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Assemblée Nationale', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Sénat', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Ambassade du Gabon en France', 'Ministère', 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'Conseil National de la Communication', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `etablissement`
--

CREATE TABLE `etablissement` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `ville_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `etablissement`
--

INSERT INTO `etablissement` (`id`, `nom`, `type`, `ville_id`) VALUES
(1, 'Université Omar Bongo', 'Université', 1),
(2, 'Lycée National Léon Mba', 'Lycée', 1),
(3, 'Institut National des Sciences', 'Institut', 2),
(4, 'École Nationale d’Administration', 'École', 1),
(5, 'Université des Sciences de la Santé', 'Université', 1);

-- --------------------------------------------------------

--
-- Structure de la table `experiences`
--

CREATE TABLE `experiences` (
  `id` int(11) NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `intitule` varchar(150) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `structure_id` int(11) DEFAULT NULL,
  `justificatif_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `experiences`
--

INSERT INTO `experiences` (`id`, `dignitaire_id`, `intitule`, `date_debut`, `date_fin`, `structure_id`, `justificatif_path`) VALUES
(1, 1, 'Avocat à la Cour', '1985-01-01', '1990-12-31', 1, NULL),
(2, 2, 'Directrice Cabinet Ministériel', '2000-04-10', '2005-09-15', 5, NULL),
(3, 3, 'Ingénieur Systèmes', '2002-05-05', '2010-07-30', 2, NULL),
(4, 4, 'Chargée de Mission', '2007-03-01', '2014-02-28', 3, NULL),
(5, 5, 'Chef de Service', '2011-11-01', '2016-06-30', 4, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `fonctions`
--

CREATE TABLE `fonctions` (
  `id` int(11) NOT NULL,
  `fonction_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `fonctions`
--

INSERT INTO `fonctions` (`id`, `fonction_name`) VALUES
(2, 'Éduc. & Qualif.'),
(5, 'Géographie'),
(1, 'Gest. Pers.'),
(4, 'Langues'),
(7, 'Organisation'),
(3, 'Parcours Pro.'),
(6, 'Récomp. & Rec.');

-- --------------------------------------------------------

--
-- Structure de la table `historique_nominations`
--

CREATE TABLE `historique_nominations` (
  `id` int(11) NOT NULL,
  `nomination_id` int(11) NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `poste_id` int(11) DEFAULT NULL,
  `entite_id` int(11) DEFAULT NULL,
  `date_nomination` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `description` text,
  `date_modification` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `historique_nominations`
--

INSERT INTO `historique_nominations` (`id`, `nomination_id`, `dignitaire_id`, `poste_id`, `entite_id`, `date_nomination`, `date_fin`, `description`, `date_modification`) VALUES
(1, 1, 1, 1, 1, '2000-01-01', '2005-12-31', 'Premier mandat', '2026-05-21 01:14:48'),
(2, 2, 2, 2, 2, '2005-01-01', '2010-12-31', 'Ministre de la Défense', '2026-05-21 01:14:48');

-- --------------------------------------------------------

--
-- Structure de la table `langue`
--

CREATE TABLE `langue` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `code_iso` varchar(10) DEFAULT NULL,
  `famille` varchar(100) DEFAULT NULL,
  `nb_locuteurs` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `langue`
--

INSERT INTO `langue` (`id`, `nom`, `code_iso`, `famille`, `nb_locuteurs`) VALUES
(1, 'Français', NULL, NULL, NULL),
(2, 'Anglais', NULL, NULL, NULL),
(3, 'Espagnol', NULL, NULL, NULL),
(4, 'Fang', NULL, NULL, NULL),
(5, 'Myéné', NULL, NULL, NULL),
(6, 'Punu', NULL, NULL, NULL),
(7, 'Nzebi', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `langues`
--

CREATE TABLE `langues` (
  `id` int(11) NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `langue_id` int(11) NOT NULL,
  `niveau` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `langues`
--

INSERT INTO `langues` (`id`, `dignitaire_id`, `langue_id`, `niveau`) VALUES
(1, 1, 1, 'Courant'),
(2, 1, 2, 'Moyen'),
(3, 2, 1, 'Courant'),
(4, 2, 4, 'Débutant'),
(5, 3, 5, 'Bilingue');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_05_21_154228_add_continent_to_region_table', 1),
(2, '2026_05_21_160000_consolidated_schema_updates', 2),
(3, '2026_06_02_190254_add_photo_column_to_users_table', 3),
(4, '2026_06_02_200211_add_telephone_column_to_users_table', 4),
(5, '2026_06_17_100000_create_candidats_table', 5),
(6, '2026_06_17_100001_create_candidat_documents_table', 5),
(7, '2026_06_17_100002_create_conjoints_table', 5),
(8, '2026_07_01_100000_create_candidat_diplomes_table', 6),
(9, '2026_07_01_100001_create_candidat_langues_table', 6),
(10, '2026_07_01_100002_create_candidat_experiences_table', 6),
(11, '2026_07_01_100003_add_justificatif_path_to_diplome_and_experiences', 6),
(12, '2026_07_01_120000_create_audit_logs_table', 7),
(13, '2026_07_02_090000_add_statut_to_nominations_and_postes', 8),
(14, '2026_07_02_100000_add_permissions_granulaires', 9),
(15, '2026_07_02_110000_add_statut_to_dignitaire', 10),
(16, '2026_07_04_090000_fix_dignitaire_emails_telephones_fk_types', 11),
(17, '2026_07_04_100000_add_rappel_envoye_to_nominations', 12),
(18, '2026_07_05_000000_create_rapports_table', 13),
(19, '2026_07_05_100000_create_dignitaire_documents_table', 14),
(20, '2026_07_10_090000_add_type_to_candidat_diplomes_table', 15),
(21, '2026_07_10_100000_add_nomination_proof_fields_to_nominations_table', 16),
(22, '2026_07_10_110000_add_contact_fields_to_entite_table', 17),
(23, '2026_07_10_101001_add_code_iso_famille_locuteurs_to_langue_table', 18),
(24, '2026_07_10_101026_add_entite_rattachement_to_entite_table', 18),
(25, '2026_07_10_101029_add_date_prise_fonction_to_dignitaire_table', 18),
(26, '2026_07_10_110747_create_candidat_messages_table', 19),
(27, '2026_07_20_143408_create_affectations_table', 20),
(28, '2026_07_20_143415_add_nationalite_id_to_dignitaire_table', 20),
(29, '2026_07_20_164015_create_password_reset_tokens_table', 21),
(30, '2026_07_20_204818_create_parametres_table', 22),
(31, '2026_07_20_204821_create_otp_codes_table', 22),
(32, '2026_07_20_204824_add_email_verifie_le_to_candidats_table', 22),
(33, '2026_07_20_231817_fix_timestamp_columns_timezone_bug', 23),
(34, '2026_07_22_153319_add_poste_id_to_affectations_table', 24),
(35, '2026_07_22_160531_add_nature_to_affectations_table', 25),
(36, '2026_07_23_002550_add_entite_sousfonction', 26),
(38, '2026_08_05_121612_add_validation_columns_to_candidat_tables', 27),
(39, '2026_08_05_121829_create_admin_notifications_table', 28);

-- --------------------------------------------------------

--
-- Structure de la table `nominations`
--

CREATE TABLE `nominations` (
  `id` int(11) NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `entite_id` int(11) DEFAULT NULL,
  `poste_id` int(11) DEFAULT NULL,
  `pv_id` int(11) DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('en_cours','terminee') NOT NULL DEFAULT 'en_cours',
  `motif_fin` enum('fin_fonction','mise_a_disposition') DEFAULT NULL,
  `rappel_envoye` tinyint(1) NOT NULL DEFAULT '0',
  `fonction` varchar(150) DEFAULT NULL,
  `type_nomination` varchar(30) DEFAULT NULL,
  `autorite_nominatrice` varchar(255) DEFAULT NULL,
  `numero_decret` varchar(100) DEFAULT NULL,
  `document_nomination_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `nominations`
--

INSERT INTO `nominations` (`id`, `dignitaire_id`, `entite_id`, `poste_id`, `pv_id`, `date_debut`, `date_fin`, `statut`, `motif_fin`, `rappel_envoye`, `fonction`, `type_nomination`, `autorite_nominatrice`, `numero_decret`, `document_nomination_path`) VALUES
(1, 1, 1, NULL, NULL, '2000-01-01', '2005-12-31', 'terminee', 'fin_fonction', 0, 'Président de la République', NULL, NULL, NULL, NULL),
(2, 2, 2, NULL, NULL, '2005-01-01', '2010-12-31', 'terminee', 'fin_fonction', 0, 'Ministre de la Défense', NULL, NULL, NULL, NULL),
(3, 3, 3, NULL, NULL, '2010-01-01', '2015-12-31', 'terminee', 'fin_fonction', 0, 'Ministre de l’Intérieur', NULL, NULL, NULL, NULL),
(4, 4, 4, NULL, NULL, '2015-01-01', '2020-12-31', 'terminee', 'fin_fonction', 0, 'Présidente de l’Assemblée Nationale', NULL, NULL, NULL, NULL),
(5, 5, 5, NULL, NULL, '2020-01-01', NULL, 'en_cours', NULL, 0, 'Sénateur', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purpose` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tentatives` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parametres`
--

CREATE TABLE `parametres` (
  `cle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `parametres`
--

INSERT INTO `parametres` (`cle`, `valeur`, `created_at`, `updated_at`) VALUES
('otp_login_admin_enabled', '1', '2026-07-22 14:16:29', '2026-07-22 14:16:29'),
('otp_login_candidat_enabled', '0', '2026-07-22 14:16:30', '2026-07-22 14:16:29');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pays`
--

CREATE TABLE `pays` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `code_iso` varchar(3) DEFAULT NULL,
  `indicatif` varchar(10) NOT NULL,
  `continent` varchar(50) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `pays`
--

INSERT INTO `pays` (`id`, `nom`, `code_iso`, `indicatif`, `continent`, `region_id`) VALUES
(1, 'Afrique du Sud', 'ZA', '+27', 'Afrique', 5),
(2, 'Algérie', 'DZ', '+213', 'Afrique', 1),
(3, 'Angola', 'AO', '+244', 'Afrique', 5),
(4, 'Bénin', 'BJ', '+229', 'Afrique', 2),
(5, 'Cameroun', 'CM', '+237', 'Afrique', 3),
(6, 'Congo Brazaville', 'CG', '+242', 'Afrique', 3),
(7, 'RD Congo', 'CD', '+243', 'Afrique', 3),
(8, 'Côte dIvoire', 'CI', '+225', 'Afrique', 2),
(9, 'Égypte', 'EG', '+20', 'Afrique', 1),
(10, 'Éthiopie', 'ET', '+251', 'Afrique', 4),
(11, 'Guinée équatoriale', 'GQ', '+240', 'Afrique', 3),
(12, 'Libye', 'LY', '+218', 'Afrique', 1),
(13, 'Mali', 'ML', '+223', 'Afrique', 2),
(14, 'Maroc', 'MA', '+212', 'Afrique', 1),
(15, 'Nigeria', 'NG', '+234', 'Afrique', 2),
(16, 'Sao Tomé-et-Principe', 'ST', '+239', 'Afrique', 3),
(17, 'Sénégal', 'SN', '+221', 'Afrique', 2),
(18, 'Togo', 'TG', '+228', 'Afrique', 2),
(19, 'Tunisie', 'TN', '+216', 'Afrique', 1),
(20, 'Brésil', 'BR', '+55', 'Amérique', 16),
(21, 'Canada', 'CA', '+1', 'Amérique', 14),
(22, 'Cuba', 'CU', '+53', 'Amérique', 17),
(23, 'États-Unis', 'US', '+1', 'Amérique', 14),
(24, 'Arabie saoudite', 'SA', '+966', 'Asie', 10),
(25, 'Chine', 'CN', '+86', 'Asie', 9),
(26, 'Corée du Sud', 'KR', '+82', 'Asie', 13),
(27, 'Inde', 'IN', '+91', 'Asie', 12),
(28, 'Japon', 'JP', '+81', 'Asie', 13),
(29, 'Liban', 'LB', '+961', 'Asie', 10),
(30, 'Turquie', 'TR', '+90', 'Asie', 8),
(31, 'Allemagne', 'DE', '+49', 'Europe', 6),
(32, 'Belgique', 'BE', '+32', 'Europe', 6),
(33, 'Espagne', 'ES', '+34', 'Europe', 8),
(34, 'France', 'FR', '+33', 'Europe', 6),
(35, 'Italie', 'IT', '+39', 'Europe', 8),
(36, 'Royaume-Uni', 'GB', '+44', 'Europe', 6),
(37, 'Russie', 'RU', '+7', 'Europe', 7),
(38, 'Vatican', 'VA', '+379', 'Europe', 8),
(39, 'République centrafricaine', 'CF', '+236', 'Afrique', 3),
(40, 'Gabon', 'GA', '+241', 'Afrique', 3);

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(7, 'App\\Models\\Candidat', 1, 'candidat-token', 'c609bb1e67d9c564c9f6fc8be096705d8d34c8e8e5bbd45594dbff90c55daa5f', '[\"*\"]', '2026-07-01 10:55:50', NULL, '2026-07-01 10:55:02', '2026-07-01 10:55:50'),
(16, 'App\\Models\\Candidat', 5, 'candidat-token', '081f7268c5f5a4517d2f1195d7369586ce4cb0c25346a1e7f0b3bf3d88304ac4', '[\"*\"]', NULL, NULL, '2026-07-01 21:40:35', '2026-07-01 21:40:35'),
(32, 'App\\Models\\Candidat', 3, 'candidat-token', '85a38a98009435489fd96cb6aedbe768212d45bf2b7c3fb15fc44d5751fee7b1', '[\"*\"]', '2026-07-11 00:57:03', '2026-07-17 10:30:56', '2026-07-10 10:30:56', '2026-07-11 00:57:03'),
(47, 'App\\Models\\User', 11, 'auth-token', 'eb21efeb2ea19c2808163ac5e6793a148f2637338c6fcc29337e48dbb31201ed', '[\"*\"]', '2026-08-05 12:53:53', '2026-08-11 12:16:06', '2026-08-04 12:16:06', '2026-08-05 12:53:53'),
(48, 'App\\Models\\Candidat', 4, 'candidat-token', 'c5b470957214283b8c98d2e237cc047ea9f2b5f854007400e4a6608d80e3fc4c', '[\"*\"]', '2026-08-05 12:52:52', '2026-08-11 12:21:09', '2026-08-04 12:21:09', '2026-08-05 12:52:52');

-- --------------------------------------------------------

--
-- Structure de la table `postes`
--

CREATE TABLE `postes` (
  `id` int(11) NOT NULL,
  `dignitaire_id` int(11) NOT NULL,
  `intitule` varchar(150) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('en_cours','terminee') NOT NULL DEFAULT 'en_cours',
  `motif_fin` enum('fin_fonction','mise_a_disposition') DEFAULT NULL,
  `entite_id` int(11) DEFAULT NULL,
  `ville_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `postes`
--

INSERT INTO `postes` (`id`, `dignitaire_id`, `intitule`, `date_debut`, `date_fin`, `statut`, `motif_fin`, `entite_id`, `ville_id`) VALUES
(1, 1, 'Président de la République', '2009-10-16', '2025-04-12', 'terminee', 'fin_fonction', 1, 1),
(2, 2, 'Ministre de l\'Intérieur', '2010-03-20', '2016-08-05', 'terminee', 'fin_fonction', 2, 2),
(3, 3, 'Député', '2014-02-12', NULL, 'en_cours', NULL, 4, 3),
(4, 4, 'Ambassadrice', '2017-09-08', '2022-06-30', 'terminee', 'fin_fonction', 6, 4),
(5, 5, 'Sénateur', '2015-07-01', '2021-09-25', 'terminee', 'fin_fonction', 5, 4),
(6, 6, 'Directeur Général', '2016-01-15', NULL, 'en_cours', NULL, 2, 2),
(7, 7, 'Secrétaire Général', '2018-10-01', NULL, 'en_cours', NULL, 2, 5),
(8, 8, 'Chef de Service', '2008-03-17', '2017-04-20', 'terminee', 'fin_fonction', 2, 1),
(9, 9, 'Conseiller Spécial', '2019-06-11', '2023-01-01', 'terminee', 'fin_fonction', 1, 6),
(10, 10, 'Directeur de Cabinet', '2012-10-10', NULL, 'en_cours', NULL, 7, 7),
(11, 11, 'Gouverneure', '2015-06-14', '2022-02-10', 'terminee', 'fin_fonction', 7, 2),
(12, 12, 'Attachée de Presse', '2022-01-20', '2011-11-30', 'terminee', 'fin_fonction', 6, 3),
(13, 13, 'Conseillère Diplomatique', '2021-02-18', NULL, 'en_cours', NULL, 1, 4),
(14, 14, 'Directeur de Finances', '2010-02-22', '2017-07-15', 'terminee', 'fin_fonction', 1, 6),
(15, 15, 'Présidente du Sénat', '2020-05-14', NULL, 'en_cours', NULL, 7, 7);

-- --------------------------------------------------------

--
-- Structure de la table `pv`
--

CREATE TABLE `pv` (
  `id` int(11) NOT NULL,
  `numero` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `pv`
--

INSERT INTO `pv` (`id`, `numero`, `date`, `description`) VALUES
(1, 'PV2025-001', '2025-01-15', 'Procès-verbal de nomination'),
(2, 'PV2025-002', '2025-03-10', 'Procès-verbal de réunion du conseil');

-- --------------------------------------------------------

--
-- Structure de la table `rapports`
--

CREATE TABLE `rapports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `periode_debut` date NOT NULL,
  `periode_fin` date NOT NULL,
  `nom_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chemin_fichier` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taille_octets` bigint(20) UNSIGNED DEFAULT NULL,
  `genere_le` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `region`
--

CREATE TABLE `region` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `continent` varchar(100) DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'region',
  `pays_nom` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `region`
--

INSERT INTO `region` (`id`, `nom`, `continent`, `type`, `pays_nom`) VALUES
(1, 'Afrique du Nord', 'Afrique', 'region', NULL),
(2, 'Afrique de l’Ouest', 'Afrique', 'region', NULL),
(3, 'Afrique centrale', 'Afrique', 'region', NULL),
(4, 'Afrique de l’Est', 'Afrique', 'region', NULL),
(5, 'Afrique australe', 'Afrique', 'region', NULL),
(6, 'Europe de l’Ouest', 'Europe', 'region', NULL),
(7, 'Europe centrale et orientale', 'Europe', 'region', NULL),
(8, 'Europe du Sud', 'Europe', 'region', NULL),
(9, 'Asie centrale', 'Asie', 'region', NULL),
(10, 'Moyen-Orient', 'Asie', 'region', NULL),
(11, 'Asie du Sud-Est', 'Asie', 'region', NULL),
(12, 'Asie du Sud', 'Asie', 'region', NULL),
(13, 'Asie orientale', 'Asie', 'region', NULL),
(14, 'Amérique du Nord', 'Amérique', 'region', NULL),
(15, 'Amérique centrale', 'Amérique', 'region', NULL),
(16, 'Amérique du Sud', 'Amérique', 'region', NULL),
(17, 'Caraïbes', 'Amérique', 'region', NULL),
(18, 'Océanie', 'Océanie', 'region', NULL),
(20, 'Haut-Ogooué', NULL, 'province', 'Gabon'),
(21, 'Moyen-Ogooué', NULL, 'province', 'Gabon'),
(22, 'Ngounié', NULL, 'province', 'Gabon'),
(23, 'Nyanga', NULL, 'province', 'Gabon'),
(24, 'Ogooué-Ivindo', NULL, 'province', 'Gabon'),
(25, 'Ogooué-Lolo', NULL, 'province', 'Gabon'),
(26, 'Ogooué-Maritime', NULL, 'province', 'Gabon'),
(27, 'Woleu-Ntem', NULL, 'province', 'Gabon'),
(28, 'Estuaire', NULL, 'province', 'Gabon'),
(30, 'Île-de-France', NULL, 'province', 'France'),
(31, 'Provence-Alpes-Côte d\'Azur', NULL, 'province', 'France'),
(32, 'Auvergne-Rhône-Alpes', NULL, 'province', 'France'),
(33, 'Nouvelle-Aquitaine', NULL, 'province', 'France'),
(34, 'Occitanie', NULL, 'province', 'France'),
(35, 'Centre', NULL, 'province', 'Cameroun'),
(36, 'Littoral', NULL, 'province', 'Bénin'),
(37, 'Ouest', NULL, 'province', 'Cameroun'),
(38, 'Nord-Ouest', NULL, 'province', 'Cameroun'),
(39, 'Sud-Ouest', NULL, 'province', 'Cameroun'),
(40, 'Dakar', NULL, 'province', 'Sénégal'),
(41, 'Thiès', NULL, 'province', 'Sénégal'),
(42, 'Saint-Louis', NULL, 'province', 'Sénégal'),
(43, 'Diourbel', NULL, 'province', 'Sénégal'),
(44, 'Kaolack', NULL, 'province', 'Sénégal'),
(45, 'Abidjan', NULL, 'province', 'Côte dIvoire'),
(46, 'Yamoussoukro', NULL, 'province', 'Côte dIvoire'),
(47, 'Bouaké', NULL, 'province', 'Côte dIvoire'),
(48, 'San-Pédro', NULL, 'province', 'Côte dIvoire'),
(49, 'Korhogo', NULL, 'province', 'Côte dIvoire'),
(50, 'Brazzaville', NULL, 'province', 'Congo Brazaville'),
(51, 'Pointe-Noire', NULL, 'province', 'Congo Brazaville'),
(52, 'Plateaux', NULL, 'province', 'Togo'),
(53, 'Cuvette', NULL, 'province', 'Congo Brazaville'),
(54, 'Sangha', NULL, 'province', 'Congo Brazaville'),
(55, 'Kinshasa', NULL, 'province', 'RD Congo'),
(56, 'Kongo-Central', NULL, 'province', 'RD Congo'),
(57, 'Katanga', NULL, 'province', 'RD Congo'),
(58, 'Kasaï', NULL, 'province', 'RD Congo'),
(59, 'Nord-Kivu', NULL, 'province', 'RD Congo'),
(60, 'Atlantique', NULL, 'province', 'Bénin'),
(61, 'Ouémé', NULL, 'province', 'Bénin'),
(62, 'Borgou', NULL, 'province', 'Bénin'),
(63, 'Zou', NULL, 'province', 'Bénin'),
(64, 'Maritime', NULL, 'province', 'Togo'),
(65, 'Centrale', NULL, 'province', 'Togo'),
(66, 'Kara', NULL, 'province', 'Togo'),
(67, 'Savanes', NULL, 'province', 'Togo'),
(68, 'Bamako', NULL, 'province', 'Mali'),
(69, 'Kayes', NULL, 'province', 'Mali'),
(70, 'Koulikoro', NULL, 'province', 'Mali'),
(71, 'Sikasso', NULL, 'province', 'Mali'),
(72, 'Ségou', NULL, 'province', 'Mali'),
(73, 'Lagos', NULL, 'province', 'Nigeria'),
(74, 'Abuja', NULL, 'province', 'Nigeria'),
(75, 'Kano', NULL, 'province', 'Nigeria'),
(76, 'Rivers', NULL, 'province', 'Nigeria'),
(77, 'Oyo', NULL, 'province', 'Nigeria'),
(78, 'Casablanca-Settat', NULL, 'province', 'Maroc'),
(79, 'Rabat-Salé-Kénitra', NULL, 'province', 'Maroc'),
(80, 'Fès-Meknès', NULL, 'province', 'Maroc'),
(81, 'Marrakech-Safi', NULL, 'province', 'Maroc'),
(82, 'Tanger-Tétouan-Al Hoceïma', NULL, 'province', 'Maroc'),
(83, 'Alger', NULL, 'province', 'Algérie'),
(84, 'Oran', NULL, 'province', 'Algérie'),
(85, 'Constantine', NULL, 'province', 'Algérie'),
(86, 'Annaba', NULL, 'province', 'Algérie'),
(87, 'Blida', NULL, 'province', 'Algérie'),
(88, 'Tunis', NULL, 'province', 'Tunisie'),
(89, 'Sfax', NULL, 'province', 'Tunisie'),
(90, 'Sousse', NULL, 'province', 'Tunisie'),
(91, 'Kairouan', NULL, 'province', 'Tunisie'),
(92, 'Bizerte', NULL, 'province', 'Tunisie'),
(93, 'Le Caire', NULL, 'province', 'Égypte'),
(94, 'Alexandrie', NULL, 'province', 'Égypte'),
(95, 'Gizeh', NULL, 'province', 'Égypte'),
(96, 'Charm el-Cheikh', NULL, 'province', 'Égypte'),
(97, 'Louxor', NULL, 'province', 'Égypte'),
(98, 'Gauteng', NULL, 'province', 'Afrique du Sud'),
(99, 'Western Cape', NULL, 'province', 'Afrique du Sud'),
(100, 'KwaZulu-Natal', NULL, 'province', 'Afrique du Sud'),
(101, 'Eastern Cape', NULL, 'province', 'Afrique du Sud'),
(102, 'Mpumalanga', NULL, 'province', 'Afrique du Sud'),
(103, 'Addis-Abeba', NULL, 'province', 'Éthiopie'),
(104, 'Oromia', NULL, 'province', 'Éthiopie'),
(105, 'Amhara', NULL, 'province', 'Éthiopie'),
(106, 'Tigré', NULL, 'province', 'Éthiopie'),
(107, 'Somali', NULL, 'province', 'Éthiopie'),
(108, 'Luanda', NULL, 'province', 'Angola'),
(109, 'Huambo', NULL, 'province', 'Angola'),
(110, 'Benguela', NULL, 'province', 'Angola'),
(111, 'Cabinda', NULL, 'province', 'Angola'),
(112, 'Huíla', NULL, 'province', 'Angola'),
(113, 'Bioko Norte', NULL, 'province', 'Guinée équatoriale'),
(114, 'Litoral', NULL, 'province', 'Guinée équatoriale'),
(115, 'Centro Sur', NULL, 'province', 'Guinée équatoriale'),
(116, 'Wele-Nzas', NULL, 'province', 'Guinée équatoriale'),
(117, 'São Tomé', NULL, 'province', 'Sao Tomé-et-Principe'),
(118, 'Príncipe', NULL, 'province', 'Sao Tomé-et-Principe'),
(119, 'Tripoli', NULL, 'province', 'Libye'),
(120, 'Benghazi', NULL, 'province', 'Libye'),
(121, 'Misrata', NULL, 'province', 'Libye'),
(122, 'Sabha', NULL, 'province', 'Libye'),
(123, 'Bangui', NULL, 'province', 'République centrafricaine'),
(124, 'Ombella-M\'Poko', NULL, 'province', 'République centrafricaine'),
(125, 'Ouham', NULL, 'province', 'République centrafricaine'),
(126, 'Lobaye', NULL, 'province', 'République centrafricaine'),
(127, 'Californie', NULL, 'province', 'États-Unis'),
(128, 'New York', NULL, 'province', 'États-Unis'),
(129, 'Texas', NULL, 'province', 'États-Unis'),
(130, 'Floride', NULL, 'province', 'États-Unis'),
(131, 'Illinois', NULL, 'province', 'États-Unis'),
(132, 'Ontario', NULL, 'province', 'Canada'),
(133, 'Québec', NULL, 'province', 'Canada'),
(134, 'Colombie-Britannique', NULL, 'province', 'Canada'),
(135, 'Alberta', NULL, 'province', 'Canada'),
(136, 'Manitoba', NULL, 'province', 'Canada'),
(137, 'São Paulo', NULL, 'province', 'Brésil'),
(138, 'Rio de Janeiro', NULL, 'province', 'Brésil'),
(139, 'Minas Gerais', NULL, 'province', 'Brésil'),
(140, 'Bahia', NULL, 'province', 'Brésil'),
(141, 'Paraná', NULL, 'province', 'Brésil'),
(142, 'La Havane', NULL, 'province', 'Cuba'),
(143, 'Santiago de Cuba', NULL, 'province', 'Cuba'),
(144, 'Holguín', NULL, 'province', 'Cuba'),
(145, 'Camagüey', NULL, 'province', 'Cuba'),
(146, 'Pékin', NULL, 'province', 'Chine'),
(147, 'Shanghai', NULL, 'province', 'Chine'),
(148, 'Guangdong', NULL, 'province', 'Chine'),
(149, 'Zhejiang', NULL, 'province', 'Chine'),
(150, 'Jiangsu', NULL, 'province', 'Chine'),
(151, 'Tokyo', NULL, 'province', 'Japon'),
(152, 'Osaka', NULL, 'province', 'Japon'),
(153, 'Kyoto', NULL, 'province', 'Japon'),
(154, 'Hokkaido', NULL, 'province', 'Japon'),
(155, 'Fukuoka', NULL, 'province', 'Japon'),
(156, 'Maharashtra', NULL, 'province', 'Inde'),
(157, 'Delhi', NULL, 'province', 'Inde'),
(158, 'Karnataka', NULL, 'province', 'Inde'),
(159, 'Tamil Nadu', NULL, 'province', 'Inde'),
(160, 'Gujarat', NULL, 'province', 'Inde'),
(161, 'Séoul', NULL, 'province', 'Corée du Sud'),
(162, 'Busan', NULL, 'province', 'Corée du Sud'),
(163, 'Incheon', NULL, 'province', 'Corée du Sud'),
(164, 'Daegu', NULL, 'province', 'Corée du Sud'),
(165, 'Daejeon', NULL, 'province', 'Corée du Sud'),
(166, 'Riyad', NULL, 'province', 'Arabie saoudite'),
(167, 'La Mecque', NULL, 'province', 'Arabie saoudite'),
(168, 'Médine', NULL, 'province', 'Arabie saoudite'),
(169, 'Province orientale', NULL, 'province', 'Arabie saoudite'),
(170, 'Istanbul', NULL, 'province', 'Turquie'),
(171, 'Ankara', NULL, 'province', 'Turquie'),
(172, 'Izmir', NULL, 'province', 'Turquie'),
(173, 'Antalya', NULL, 'province', 'Turquie'),
(174, 'Bursa', NULL, 'province', 'Turquie'),
(175, 'Beyrouth', NULL, 'province', 'Liban'),
(176, 'Mont-Liban', NULL, 'province', 'Liban'),
(177, 'Nord', NULL, 'province', 'Liban'),
(178, 'Sud', NULL, 'province', 'Liban'),
(179, 'Bavière', NULL, 'province', 'Allemagne'),
(180, 'Rhénanie-du-Nord-Westphalie', NULL, 'province', 'Allemagne'),
(181, 'Bade-Wurtemberg', NULL, 'province', 'Allemagne'),
(182, 'Berlin', NULL, 'province', 'Allemagne'),
(183, 'Hambourg', NULL, 'province', 'Allemagne'),
(184, 'Bruxelles-Capitale', NULL, 'province', 'Belgique'),
(185, 'Flandre-Occidentale', NULL, 'province', 'Belgique'),
(186, 'Anvers', NULL, 'province', 'Belgique'),
(187, 'Liège', NULL, 'province', 'Belgique'),
(188, 'Hainaut', NULL, 'province', 'Belgique'),
(189, 'Madrid', NULL, 'province', 'Espagne'),
(190, 'Catalogne', NULL, 'province', 'Espagne'),
(191, 'Andalousie', NULL, 'province', 'Espagne'),
(192, 'Valence', NULL, 'province', 'Espagne'),
(193, 'Pays basque', NULL, 'province', 'Espagne'),
(194, 'Latium', NULL, 'province', 'Italie'),
(195, 'Lombardie', NULL, 'province', 'Italie'),
(196, 'Campanie', NULL, 'province', 'Italie'),
(197, 'Sicile', NULL, 'province', 'Italie'),
(198, 'Vénétie', NULL, 'province', 'Italie'),
(199, 'Angleterre', NULL, 'province', 'Royaume-Uni'),
(200, 'Écosse', NULL, 'province', 'Royaume-Uni'),
(201, 'Pays de Galles', NULL, 'province', 'Royaume-Uni'),
(202, 'Irlande du Nord', NULL, 'province', 'Royaume-Uni'),
(203, 'Moscou', NULL, 'province', 'Russie'),
(204, 'Saint-Pétersbourg', NULL, 'province', 'Russie'),
(205, 'Tatarstan', NULL, 'province', 'Russie'),
(206, 'Krasnodar', NULL, 'province', 'Russie'),
(207, 'Sverdlovsk', NULL, 'province', 'Russie'),
(208, 'Vatican', NULL, 'province', 'Vatican');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(4, 'Administrateur'),
(2, 'Assistant'),
(3, 'Gestionnaire'),
(1, 'Super Administrateur');

-- --------------------------------------------------------

--
-- Structure de la table `roles_fonctions`
--

CREATE TABLE `roles_fonctions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `fonction_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `roles_fonctions`
--

INSERT INTO `roles_fonctions` (`id`, `role_id`, `fonction_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `roles_sousfonctions`
--

CREATE TABLE `roles_sousfonctions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `sousfonction_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `roles_sousfonctions`
--

INSERT INTO `roles_sousfonctions` (`id`, `role_id`, `sousfonction_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `sousfonctions`
--

CREATE TABLE `sousfonctions` (
  `id` int(11) NOT NULL,
  `sousfonction_name` varchar(50) NOT NULL,
  `fonction_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `sousfonctions`
--

INSERT INTO `sousfonctions` (`id`, `sousfonction_name`, `fonction_id`) VALUES
(1, 'Enfant', 1),
(2, 'Dignitaire', 1),
(3, 'Poste', 7),
(4, 'Diplôme', 2),
(5, 'Expérience', 3),
(6, 'Langues', 4),
(7, 'Pays', 5),
(8, 'Ville', 5),
(9, 'Nomination', 6),
(10, 'Décoration', 6),
(11, 'Structure', 7),
(12, 'Région', 5),
(13, 'Entité', 7);

-- --------------------------------------------------------

--
-- Structure de la table `structure`
--

CREATE TABLE `structure` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `adresse` text,
  `ville_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `structure`
--

INSERT INTO `structure` (`id`, `nom`, `type`, `adresse`, `ville_id`) VALUES
(1, 'Université Omar Bongo', NULL, NULL, NULL),
(2, 'Total Gabon', NULL, NULL, NULL),
(3, 'Banque des États de l’Afrique Centrale', NULL, NULL, NULL),
(4, 'Port Autonome de Libreville', NULL, NULL, NULL),
(5, 'Ministère des Finances', NULL, NULL, NULL),
(6, 'Hôpital d’Instruction des Armées', NULL, NULL, NULL),
(7, 'Société Gabonaise de Transport', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nom_complet` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `photo` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `nom_complet`, `password`, `email`, `role_id`, `created_at`, `photo`, `telephone`) VALUES
(9, 'Magali', 'magali', '$2y$10$AYXmwscOlXPYVAoh2C7Cwu6el6jBXWnsXYQucOjN4Gj/4uXTA3kYG', 'devgroupentreprise@gmail.com', 2, '2025-07-18 23:54:39', NULL, NULL),
(11, 'admin1', 'admin1', '$2y$10$XL4iRcIms4z.HMXFlFZwrOjqLlG0h/W6hC7Et1Xjegda9OvXbXCR6', 'georgesrapontchombo22@gmail.com', 1, '2025-07-19 00:39:54', '1780624409_11.png', NULL),
(12, 'dorkas', 'Akanda', '$2y$10$kAwP44FRid.N78l7yg0SkObRW7JIMlqeeEfW2IUrBtlZLp0TialrG', 'georgeschristian2202@gmail.com', 2, '2025-07-19 00:56:44', NULL, NULL),
(14, 'Magali dorkas Akanda rut', 'grace', '$2y$10$EDQYWuyoJwsfCWaJLLnyBOVfiC0KBhkAux0/O7Ei8L4jO0C4/w24i', 'rapontchombogeorges22@gmail.com', 2, '2025-07-19 01:22:26', NULL, NULL),
(16, 'tito', 'tito', '$2y$10$wvdzxV7QOUxEFsNvK2lgg.V7n/qP03NFDIYlsElOfkkvN3dTt8wCa', 'tito@gmail.com', 1, '2025-07-19 02:34:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_fonctions`
--

CREATE TABLE `user_fonctions` (
  `user_id` int(11) NOT NULL,
  `fonction_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `user_fonctions`
--

INSERT INTO `user_fonctions` (`user_id`, `fonction_id`) VALUES
(9, 1),
(11, 1),
(12, 1),
(16, 1),
(11, 2),
(14, 2),
(11, 3),
(11, 4),
(11, 5),
(16, 5),
(11, 6),
(11, 7);

-- --------------------------------------------------------

--
-- Structure de la table `user_sousfonctions`
--

CREATE TABLE `user_sousfonctions` (
  `user_id` int(11) NOT NULL,
  `sousfonction_id` int(11) NOT NULL,
  `niveau` enum('lecture','ecriture') NOT NULL DEFAULT 'lecture'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `user_sousfonctions`
--

INSERT INTO `user_sousfonctions` (`user_id`, `sousfonction_id`, `niveau`) VALUES
(9, 2, 'lecture'),
(11, 1, 'lecture'),
(11, 2, 'lecture'),
(11, 3, 'lecture'),
(11, 4, 'lecture'),
(11, 5, 'lecture'),
(11, 6, 'lecture'),
(11, 7, 'lecture'),
(11, 8, 'lecture'),
(11, 9, 'lecture'),
(11, 10, 'lecture'),
(11, 11, 'lecture'),
(11, 12, 'lecture'),
(12, 1, 'lecture'),
(14, 4, 'lecture'),
(16, 2, 'lecture'),
(16, 7, 'lecture'),
(16, 12, 'lecture');

-- --------------------------------------------------------

--
-- Structure de la table `ville`
--

CREATE TABLE `ville` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `pays_id` int(11) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `ville`
--

INSERT INTO `ville` (`id`, `nom`, `pays_id`, `region_id`) VALUES
(1, 'Pretoria', 1, NULL),
(2, 'Alger', 2, NULL),
(3, 'Luanda', 3, NULL),
(4, 'Cotonou', 4, NULL),
(5, 'Yaoundé', 5, NULL),
(6, 'Brazzaville', 6, NULL),
(7, 'Kinshasa', 7, NULL),
(8, 'Abidjan', 8, NULL),
(9, 'Le Caire', 9, NULL),
(10, 'Addis Ababa', 10, NULL),
(11, 'Malabo', 11, NULL),
(12, 'Bata', 11, NULL),
(13, 'Tripoli', 12, NULL),
(14, 'Bamako', 13, NULL),
(15, 'Rabat', 14, NULL),
(16, 'Abuja', 15, NULL),
(17, 'São Tomé', 16, NULL),
(18, 'Dakar', 17, NULL),
(19, 'Lomé', 18, NULL),
(20, 'Tunis', 19, NULL),
(21, 'Brasília', 20, NULL),
(22, 'Ottawa', 21, NULL),
(23, 'La Havane', 22, NULL),
(24, 'Washington', 23, NULL),
(25, 'Riyad', 24, NULL),
(26, 'Pékin', 25, NULL),
(27, 'Séoul', 26, NULL),
(28, 'New Delhi', 27, NULL),
(29, 'Tokyo', 28, NULL),
(30, 'Beyrouth', 29, NULL),
(31, 'Ankara', 30, NULL),
(32, 'Berlin', 31, NULL),
(33, 'Bruxelles', 32, NULL),
(34, 'Madrid', 33, NULL),
(35, 'Paris', 34, NULL),
(36, 'Rome', 35, NULL),
(37, 'Londres', 36, NULL),
(38, 'Moscou', 37, NULL),
(39, 'Rome', 38, NULL),
(40, 'Bangui', 39, NULL),
(41, 'Libreville', 40, 28),
(42, 'Ntoum', 40, 28),
(43, 'Kango', 40, 28),
(44, 'Cocobeach', 40, 28),
(45, 'Franceville', 40, 20),
(46, 'Moanda', 40, 20),
(47, 'Akiéni', 40, 20),
(48, 'Okondja', 40, 20),
(49, 'Lambaréné', 40, 21),
(50, 'Ndjolé', 40, 21),
(51, 'Bifoun', 40, 21),
(52, 'Mouila', 40, 22),
(53, 'Ndendé', 40, 22),
(54, 'Mimongo', 40, 22),
(55, 'Mbigou', 40, 22),
(56, 'Tchibanga', 40, 23),
(57, 'Mayumba', 40, 23),
(58, 'Moulengui-Binza', 40, 23),
(59, 'Makokou', 40, 24),
(60, 'Mékambo', 40, 24),
(61, 'Ovan', 40, 24),
(62, 'Booué', 40, 24),
(63, 'Koulamoutou', 40, 25),
(64, 'Lastoursville', 40, 25),
(65, 'Pana', 40, 25),
(66, 'Port-Gentil', 40, 26),
(67, 'Omboué', 40, 26),
(68, 'Gamba', 40, 26),
(69, 'Oyem', 40, 27),
(70, 'Bitam', 40, 27),
(71, 'Mitzic', 40, 27),
(72, 'Minvoul', 40, 27),
(73, 'Versailles', 34, 30),
(74, 'Boulogne-Billancourt', 34, 30),
(75, 'Saint-Denis', 34, 30),
(76, 'Marseille', 34, 31),
(77, 'Nice', 34, 31),
(78, 'Toulon', 34, 31),
(79, 'Aix-en-Provence', 34, 31),
(80, 'Lyon', 34, 32),
(81, 'Grenoble', 34, 32),
(82, 'Saint-Étienne', 34, 32),
(83, 'Clermont-Ferrand', 34, 32),
(84, 'Bordeaux', 34, 33),
(85, 'Limoges', 34, 33),
(86, 'Poitiers', 34, 33),
(87, 'La Rochelle', 34, 33),
(88, 'Toulouse', 34, 34),
(89, 'Montpellier', 34, 34),
(90, 'Nîmes', 34, 34),
(91, 'Perpignan', 34, 34),
(92, 'Mbalmayo', 5, 35),
(93, 'Obala', 5, 35),
(94, 'Akonolinga', 5, 35),
(95, 'Douala', 5, 36),
(96, 'Edéa', 5, 36),
(97, 'Nkongsamba', 5, 36),
(98, 'Bafoussam', 5, 37),
(99, 'Dschang', 5, 37),
(100, 'Mbouda', 5, 37),
(101, 'Bamenda', 5, 38),
(102, 'Kumbo', 5, 38),
(103, 'Wum', 5, 38),
(104, 'Buéa', 5, 39),
(105, 'Limbé', 5, 39),
(106, 'Kumba', 5, 39),
(107, 'Pikine', 17, 40),
(108, 'Guédiawaye', 17, 40),
(109, 'Rufisque', 17, 40),
(110, 'Thiès', 17, 41),
(111, 'Mbour', 17, 41),
(112, 'Tivaouane', 17, 41),
(113, 'Saint-Louis', 17, 42),
(114, 'Dagana', 17, 42),
(115, 'Podor', 17, 42),
(116, 'Diourbel', 17, 43),
(117, 'Touba', 17, 43),
(118, 'Mbacké', 17, 43),
(119, 'Kaolack', 17, 44),
(120, 'Nioro du Rip', 17, 44),
(121, 'Guinguinéo', 17, 44),
(122, 'Bingerville', 8, 45),
(123, 'Anyama', 8, 45),
(124, 'Yamoussoukro', 8, 46),
(125, 'Toumodi', 8, 46),
(126, 'Didiévi', 8, 46),
(127, 'Bouaké', 8, 47),
(128, 'Katiola', 8, 47),
(129, 'Sakassou', 8, 47),
(130, 'San-Pédro', 8, 48),
(131, 'Tabou', 8, 48),
(132, 'Sassandra', 8, 48),
(133, 'Korhogo', 8, 49),
(134, 'Ferkessédougou', 8, 49),
(135, 'Boundiali', 8, 49),
(136, 'Kintélé', 6, 50),
(137, 'Ignié', 6, 50),
(138, 'Pointe-Noire', 6, 51),
(139, 'Loango', 6, 51),
(140, 'Tchiamba-Nzassi', 6, 51),
(141, 'Djambala', 6, 52),
(142, 'Gamboma', 6, 52),
(143, 'Lékana', 6, 52),
(144, 'Owando', 6, 53),
(145, 'Makoua', 6, 53),
(146, 'Boundji', 6, 53),
(147, 'Ouesso', 6, 54),
(148, 'Sembé', 6, 54),
(149, 'Souanké', 6, 54),
(150, 'Ndjili', 7, 55),
(151, 'Masina', 7, 55),
(152, 'Matadi', 7, 56),
(153, 'Boma', 7, 56),
(154, 'Mbanza-Ngungu', 7, 56),
(155, 'Lubumbashi', 7, 57),
(156, 'Likasi', 7, 57),
(157, 'Kolwezi', 7, 57),
(158, 'Kananga', 7, 58),
(159, 'Tshikapa', 7, 58),
(160, 'Mbuji-Mayi', 7, 58),
(161, 'Goma', 7, 59),
(162, 'Butembo', 7, 59),
(163, 'Beni', 7, 59),
(164, 'Abomey-Calavi', 4, 60),
(165, 'Ouidah', 4, 60),
(166, 'Allada', 4, 60),
(167, 'Porto-Novo', 4, 61),
(168, 'Adjarra', 4, 61),
(169, 'Sèmè-Kpodji', 4, 61),
(170, 'Parakou', 4, 62),
(171, 'Nikki', 4, 62),
(172, 'Tchaourou', 4, 62),
(173, 'Abomey', 4, 63),
(174, 'Bohicon', 4, 63),
(175, 'Djidja', 4, 63),
(176, 'Aného', 18, 64),
(177, 'Tsévié', 18, 64),
(178, 'Atakpamé', 18, 52),
(179, 'Kpalimé', 18, 52),
(180, 'Notsé', 18, 52),
(181, 'Sokodé', 18, 65),
(182, 'Tchamba', 18, 65),
(183, 'Sotouboua', 18, 65),
(184, 'Kara', 18, 66),
(185, 'Bassar', 18, 66),
(186, 'Niamtougou', 18, 66),
(187, 'Dapaong', 18, 67),
(188, 'Mango', 18, 67),
(189, 'Cinkassé', 18, 67),
(190, 'Kayes', 13, 69),
(191, 'Kita', 13, 69),
(192, 'Nioro du Sahel', 13, 69),
(193, 'Koulikoro', 13, 70),
(194, 'Kati', 13, 70),
(195, 'Kolokani', 13, 70),
(196, 'Sikasso', 13, 71),
(197, 'Koutiala', 13, 71),
(198, 'Kadiolo', 13, 71),
(199, 'Ségou', 13, 72),
(200, 'San', 13, 72),
(201, 'Bla', 13, 72),
(202, 'Lagos', 15, 73),
(203, 'Ikeja', 15, 73),
(204, 'Epe', 15, 73),
(205, 'Gwagwalada', 15, 74),
(206, 'Kuje', 15, 74),
(207, 'Kano', 15, 75),
(208, 'Wudil', 15, 75),
(209, 'Bichi', 15, 75),
(210, 'Port Harcourt', 15, 76),
(211, 'Bonny', 15, 76),
(212, 'Okrika', 15, 76),
(213, 'Ibadan', 15, 77),
(214, 'Oyo', 15, 77),
(215, 'Ogbomosho', 15, 77),
(216, 'Casablanca', 14, 78),
(217, 'Mohammedia', 14, 78),
(218, 'El Jadida', 14, 78),
(219, 'Salé', 14, 79),
(220, 'Kénitra', 14, 79),
(221, 'Fès', 14, 80),
(222, 'Meknès', 14, 80),
(223, 'Taza', 14, 80),
(224, 'Marrakech', 14, 81),
(225, 'Safi', 14, 81),
(226, 'Essaouira', 14, 81),
(227, 'Tanger', 14, 82),
(228, 'Tétouan', 14, 82),
(229, 'Al Hoceïma', 14, 82),
(230, 'Bab El Oued', 2, 83),
(231, 'Hussein Dey', 2, 83),
(232, 'Oran', 2, 84),
(233, 'Aïn El Turk', 2, 84),
(234, 'Mers El Kébir', 2, 84),
(235, 'Constantine', 2, 85),
(236, 'El Khroub', 2, 85),
(237, 'Aïn Smara', 2, 85),
(238, 'Annaba', 2, 86),
(239, 'El Hadjar', 2, 86),
(240, 'Berrahal', 2, 86),
(241, 'Blida', 2, 87),
(242, 'Boufarik', 2, 87),
(243, 'Larbaa', 2, 87),
(244, 'La Marsa', 19, 88),
(245, 'Carthage', 19, 88),
(246, 'Sfax', 19, 89),
(247, 'Sakiet Ezzit', 19, 89),
(248, 'Sakiet Eddaïer', 19, 89),
(249, 'Sousse', 19, 90),
(250, 'Hammam Sousse', 19, 90),
(251, 'Msaken', 19, 90),
(252, 'Kairouan', 19, 91),
(253, 'Haffouz', 19, 91),
(254, 'Sbikha', 19, 91),
(255, 'Bizerte', 19, 92),
(256, 'Menzel Bourguiba', 19, 92),
(257, 'Mateur', 19, 92),
(258, 'Gizeh', 9, 93),
(259, 'Héliopolis', 9, 93),
(260, 'Alexandrie', 9, 94),
(261, 'Borg El Arab', 9, 94),
(262, 'Abou Qir', 9, 94),
(263, '6 Octobre', 9, 95),
(264, 'Cheikh Zayed', 9, 95),
(265, 'Charm el-Cheikh', 9, 96),
(266, 'Dahab', 9, 96),
(267, 'Nuweiba', 9, 96),
(268, 'Louxor', 9, 97),
(269, 'Karnak', 9, 97),
(270, 'Esna', 9, 97),
(271, 'Johannesburg', 1, 98),
(272, 'Soweto', 1, 98),
(273, 'Le Cap', 1, 99),
(274, 'Stellenbosch', 1, 99),
(275, 'Paarl', 1, 99),
(276, 'Durban', 1, 100),
(277, 'Pietermaritzburg', 1, 100),
(278, 'Richards Bay', 1, 100),
(279, 'Port Elizabeth', 1, 101),
(280, 'East London', 1, 101),
(281, 'Mthatha', 1, 101),
(282, 'Nelspruit', 1, 102),
(283, 'Witbank', 1, 102),
(284, 'Middelburg', 1, 102),
(285, 'Addis-Abeba', 10, 103),
(286, 'Bole', 10, 103),
(287, 'Kirkos', 10, 103),
(288, 'Adama', 10, 104),
(289, 'Jimma', 10, 104),
(290, 'Bishoftu', 10, 104),
(291, 'Bahir Dar', 10, 105),
(292, 'Gondar', 10, 105),
(293, 'Dessie', 10, 105),
(294, 'Mekele', 10, 106),
(295, 'Adigrat', 10, 106),
(296, 'Axoum', 10, 106),
(297, 'Jijiga', 10, 107),
(298, 'Gode', 10, 107),
(299, 'Kebri Dehar', 10, 107),
(300, 'Viana', 3, 108),
(301, 'Cacuaco', 3, 108),
(302, 'Huambo', 3, 109),
(303, 'Caála', 3, 109),
(304, 'Longonjo', 3, 109),
(305, 'Benguela', 3, 110),
(306, 'Lobito', 3, 110),
(307, 'Catumbela', 3, 110),
(308, 'Cabinda', 3, 111),
(309, 'Cacongo', 3, 111),
(310, 'Buco-Zau', 3, 111),
(311, 'Lubango', 3, 112),
(312, 'Chibia', 3, 112),
(313, 'Matala', 3, 112),
(314, 'Rebola', 11, 113),
(315, 'Baney', 11, 113),
(316, 'Mbini', 11, 114),
(317, 'Cogo', 11, 114),
(318, 'Evinayong', 11, 115),
(319, 'Acurenam', 11, 115),
(320, 'Bicurga', 11, 115),
(321, 'Mongomo', 11, 116),
(322, 'Nsork', 11, 116),
(323, 'Aconibe', 11, 116),
(324, 'Trindade', 16, 117),
(325, 'Guadalupe', 16, 117),
(326, 'Santo António', 16, 118),
(327, 'Porto Real', 16, 118),
(328, 'Tajoura', 12, 119),
(329, 'Janzour', 12, 119),
(330, 'Benghazi', 12, 120),
(331, 'Al Marj', 12, 120),
(332, 'Ajdabiya', 12, 120),
(333, 'Misrata', 12, 121),
(334, 'Zliten', 12, 121),
(335, 'Tawergha', 12, 121),
(336, 'Sabha', 12, 122),
(337, 'Ubari', 12, 122),
(338, 'Murzuq', 12, 122),
(339, 'Bimbo', 39, 124),
(340, 'Damara', 39, 124),
(341, 'Boali', 39, 124),
(342, 'Bossangoa', 39, 125),
(343, 'Bouca', 39, 125),
(344, 'Batangafo', 39, 125),
(345, 'Mbaïki', 39, 126),
(346, 'Mongoumba', 39, 126),
(347, 'Boganda', 39, 126),
(348, 'Los Angeles', 23, 127),
(349, 'San Francisco', 23, 127),
(350, 'San Diego', 23, 127),
(351, 'Sacramento', 23, 127),
(352, 'New York', 23, 128),
(353, 'Buffalo', 23, 128),
(354, 'Rochester', 23, 128),
(355, 'Albany', 23, 128),
(356, 'Houston', 23, 129),
(357, 'Dallas', 23, 129),
(358, 'Austin', 23, 129),
(359, 'San Antonio', 23, 129),
(360, 'Miami', 23, 130),
(361, 'Orlando', 23, 130),
(362, 'Tampa', 23, 130),
(363, 'Jacksonville', 23, 130),
(364, 'Chicago', 23, 131),
(365, 'Aurora', 23, 131),
(366, 'Naperville', 23, 131),
(367, 'Rockford', 23, 131),
(368, 'Toronto', 21, 132),
(369, 'Mississauga', 21, 132),
(370, 'Hamilton', 21, 132),
(371, 'Montréal', 21, 133),
(372, 'Québec', 21, 133),
(373, 'Laval', 21, 133),
(374, 'Gatineau', 21, 133),
(375, 'Vancouver', 21, 134),
(376, 'Victoria', 21, 134),
(377, 'Surrey', 21, 134),
(378, 'Burnaby', 21, 134),
(379, 'Calgary', 21, 135),
(380, 'Edmonton', 21, 135),
(381, 'Red Deer', 21, 135),
(382, 'Lethbridge', 21, 135),
(383, 'Winnipeg', 21, 136),
(384, 'Brandon', 21, 136),
(385, 'Steinbach', 21, 136),
(386, 'São Paulo', 20, 137),
(387, 'Campinas', 20, 137),
(388, 'Santos', 20, 137),
(389, 'Guarulhos', 20, 137),
(390, 'Rio de Janeiro', 20, 138),
(391, 'Niterói', 20, 138),
(392, 'Duque de Caxias', 20, 138),
(393, 'Belo Horizonte', 20, 139),
(394, 'Uberlândia', 20, 139),
(395, 'Contagem', 20, 139),
(396, 'Salvador', 20, 140),
(397, 'Feira de Santana', 20, 140),
(398, 'Vitória da Conquista', 20, 140),
(399, 'Curitiba', 20, 141),
(400, 'Londrina', 20, 141),
(401, 'Maringá', 20, 141),
(402, 'Marianao', 22, 142),
(403, 'Guanabacoa', 22, 142),
(404, 'Santiago de Cuba', 22, 143),
(405, 'Palma Soriano', 22, 143),
(406, 'Holguín', 22, 144),
(407, 'Moa', 22, 144),
(408, 'Banes', 22, 144),
(409, 'Camagüey', 22, 145),
(410, 'Florida', 22, 145),
(411, 'Nuevitas', 22, 145),
(412, 'Chaoyang', 25, 146),
(413, 'Haidian', 25, 146),
(414, 'Shanghai', 25, 147),
(415, 'Pudong', 25, 147),
(416, 'Baoshan', 25, 147),
(417, 'Guangzhou', 25, 148),
(418, 'Shenzhen', 25, 148),
(419, 'Dongguan', 25, 148),
(420, 'Hangzhou', 25, 149),
(421, 'Ningbo', 25, 149),
(422, 'Wenzhou', 25, 149),
(423, 'Nanjing', 25, 150),
(424, 'Suzhou', 25, 150),
(425, 'Wuxi', 25, 150),
(426, 'Shibuya', 28, 151),
(427, 'Shinjuku', 28, 151),
(428, 'Osaka', 28, 152),
(429, 'Sakai', 28, 152),
(430, 'Higashiosaka', 28, 152),
(431, 'Kyoto', 28, 153),
(432, 'Uji', 28, 153),
(433, 'Kameoka', 28, 153),
(434, 'Sapporo', 28, 154),
(435, 'Asahikawa', 28, 154),
(436, 'Hakodate', 28, 154),
(437, 'Fukuoka', 28, 155),
(438, 'Kitakyushu', 28, 155),
(439, 'Kurume', 28, 155),
(440, 'Mumbai', 27, 156),
(441, 'Pune', 27, 156),
(442, 'Nagpur', 27, 156),
(443, 'Delhi', 27, 157),
(444, 'Gurgaon', 27, 157),
(445, 'Bangalore', 27, 158),
(446, 'Mysore', 27, 158),
(447, 'Mangalore', 27, 158),
(448, 'Chennai', 27, 159),
(449, 'Coimbatore', 27, 159),
(450, 'Madurai', 27, 159),
(451, 'Ahmedabad', 27, 160),
(452, 'Surat', 27, 160),
(453, 'Vadodara', 27, 160),
(454, 'Gangnam', 26, 161),
(455, 'Jongno', 26, 161),
(456, 'Busan', 26, 162),
(457, 'Haeundae', 26, 162),
(458, 'Suyeong', 26, 162),
(459, 'Incheon', 26, 163),
(460, 'Namdong', 26, 163),
(461, 'Bupyeong', 26, 163),
(462, 'Daegu', 26, 164),
(463, 'Suseong', 26, 164),
(464, 'Dalseo', 26, 164),
(465, 'Daejeon', 26, 165),
(466, 'Yuseong', 26, 165),
(467, 'Seo', 26, 165),
(468, 'Diriyah', 24, 166),
(469, 'Al Kharj', 24, 166),
(470, 'La Mecque', 24, 167),
(471, 'Djeddah', 24, 167),
(472, 'Taëf', 24, 167),
(473, 'Médine', 24, 168),
(474, 'Yanbu', 24, 168),
(475, 'Al Ula', 24, 168),
(476, 'Dammam', 24, 169),
(477, 'Khobar', 24, 169),
(478, 'Dhahran', 24, 169),
(479, 'Istanbul', 30, 170),
(480, 'Kadıköy', 30, 170),
(481, 'Beşiktaş', 30, 170),
(482, 'Çankaya', 30, 171),
(483, 'Keçiören', 30, 171),
(484, 'Izmir', 30, 172),
(485, 'Konak', 30, 172),
(486, 'Karşıyaka', 30, 172),
(487, 'Antalya', 30, 173),
(488, 'Alanya', 30, 173),
(489, 'Manavgat', 30, 173),
(490, 'Bursa', 30, 174),
(491, 'Osmangazi', 30, 174),
(492, 'Nilüfer', 30, 174),
(493, 'Achrafieh', 29, 175),
(494, 'Hamra', 29, 175),
(495, 'Jounieh', 29, 176),
(496, 'Byblos', 29, 176),
(497, 'Baabda', 29, 176),
(498, 'Tripoli', 29, 177),
(499, 'Zgharta', 29, 177),
(500, 'Batroun', 29, 177),
(501, 'Sidon', 29, 178),
(502, 'Tyr', 29, 178),
(503, 'Nabatieh', 29, 178),
(504, 'Munich', 31, 179),
(505, 'Nuremberg', 31, 179),
(506, 'Augsbourg', 31, 179),
(507, 'Cologne', 31, 180),
(508, 'Düsseldorf', 31, 180),
(509, 'Dortmund', 31, 180),
(510, 'Stuttgart', 31, 181),
(511, 'Mannheim', 31, 181),
(512, 'Karlsruhe', 31, 181),
(513, 'Charlottenburg', 31, 182),
(514, 'Kreuzberg', 31, 182),
(515, 'Hambourg', 31, 183),
(516, 'Altona', 31, 183),
(517, 'Eimsbüttel', 31, 183),
(518, 'Ixelles', 32, 184),
(519, 'Schaerbeek', 32, 184),
(520, 'Bruges', 32, 185),
(521, 'Ostende', 32, 185),
(522, 'Courtrai', 32, 185),
(523, 'Anvers', 32, 186),
(524, 'Malines', 32, 186),
(525, 'Turnhout', 32, 186),
(526, 'Liège', 32, 187),
(527, 'Verviers', 32, 187),
(528, 'Seraing', 32, 187),
(529, 'Charleroi', 32, 188),
(530, 'Mons', 32, 188),
(531, 'La Louvière', 32, 188),
(532, 'Móstoles', 33, 189),
(533, 'Alcalá de Henares', 33, 189),
(534, 'Barcelone', 33, 190),
(535, 'Hospitalet', 33, 190),
(536, 'Badalona', 33, 190),
(537, 'Séville', 33, 191),
(538, 'Málaga', 33, 191),
(539, 'Cordoue', 33, 191),
(540, 'Valence', 33, 192),
(541, 'Alicante', 33, 192),
(542, 'Elche', 33, 192),
(543, 'Bilbao', 33, 193),
(544, 'Vitoria-Gasteiz', 33, 193),
(545, 'Saint-Sébastien', 33, 193),
(546, 'Latina', 35, 194),
(547, 'Frosinone', 35, 194),
(548, 'Milan', 35, 195),
(549, 'Bergame', 35, 195),
(550, 'Brescia', 35, 195),
(551, 'Naples', 35, 196),
(552, 'Salerne', 35, 196),
(553, 'Caserte', 35, 196),
(554, 'Palerme', 35, 197),
(555, 'Catane', 35, 197),
(556, 'Messine', 35, 197),
(557, 'Venise', 35, 198),
(558, 'Vérone', 35, 198),
(559, 'Padoue', 35, 198),
(560, 'Manchester', 36, 199),
(561, 'Birmingham', 36, 199),
(562, 'Liverpool', 36, 199),
(563, 'Édimbourg', 36, 200),
(564, 'Glasgow', 36, 200),
(565, 'Aberdeen', 36, 200),
(566, 'Cardiff', 36, 201),
(567, 'Swansea', 36, 201),
(568, 'Newport', 36, 201),
(569, 'Belfast', 36, 202),
(570, 'Derry', 36, 202),
(571, 'Lisburn', 36, 202),
(572, 'Khimki', 37, 203),
(573, 'Podolsk', 37, 203),
(574, 'Saint-Pétersbourg', 37, 204),
(575, 'Kolpino', 37, 204),
(576, 'Pouchkine', 37, 204),
(577, 'Kazan', 37, 205),
(578, 'Naberejnye Tchelny', 37, 205),
(579, 'Nijni Novgorod', 37, 205),
(580, 'Krasnodar', 37, 206),
(581, 'Sotchi', 37, 206),
(582, 'Novorossiysk', 37, 206),
(583, 'Iekaterinbourg', 37, 207),
(584, 'Nijni Taguil', 37, 207),
(585, 'Kamensk-Ouralski', 37, 207),
(586, 'Cité du Vatican', 38, 208);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_notifications_candidat_id_foreign` (`candidat_id`),
  ADD KEY `admin_notifications_lu_created_at_index` (`lu`,`created_at`);

--
-- Index pour la table `affectations`
--
ALTER TABLE `affectations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `affectations_dignitaire_id_index` (`dignitaire_id`),
  ADD KEY `affectations_poste_id_index` (`poste_id`);

--
-- Index pour la table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  ADD KEY `audit_logs_causer_id_index` (`causer_id`),
  ADD KEY `audit_logs_action_index` (`action`);

--
-- Index pour la table `candidats`
--
ALTER TABLE `candidats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `candidats_email_unique` (`email`),
  ADD UNIQUE KEY `candidats_nip_unique` (`nip`),
  ADD UNIQUE KEY `candidats_matricule_unique` (`matricule`),
  ADD KEY `candidats_lieu_naissance_id_foreign` (`lieu_naissance_id`),
  ADD KEY `candidats_ville_residence_id_foreign` (`ville_residence_id`),
  ADD KEY `candidats_valide_par_foreign` (`valide_par`),
  ADD KEY `candidats_dignitaire_id_foreign` (`dignitaire_id`),
  ADD KEY `candidats_statut_index` (`statut`),
  ADD KEY `candidats_email_index` (`email`),
  ADD KEY `candidats_date_candidature_index` (`date_candidature`);

--
-- Index pour la table `candidat_diplomes`
--
ALTER TABLE `candidat_diplomes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidat_diplomes_etablissement_id_foreign` (`etablissement_id`),
  ADD KEY `candidat_diplomes_ville_id_foreign` (`ville_id`),
  ADD KEY `candidat_diplomes_domaine_id_foreign` (`domaine_id`),
  ADD KEY `candidat_diplomes_candidat_id_index` (`candidat_id`);

--
-- Index pour la table `candidat_documents`
--
ALTER TABLE `candidat_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidat_documents_candidat_id_index` (`candidat_id`),
  ADD KEY `candidat_documents_type_document_index` (`type_document`);

--
-- Index pour la table `candidat_experiences`
--
ALTER TABLE `candidat_experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidat_experiences_structure_id_foreign` (`structure_id`),
  ADD KEY `candidat_experiences_candidat_id_index` (`candidat_id`);

--
-- Index pour la table `candidat_langues`
--
ALTER TABLE `candidat_langues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidat_langues_langue_id_foreign` (`langue_id`),
  ADD KEY `candidat_langues_candidat_id_index` (`candidat_id`);

--
-- Index pour la table `candidat_messages`
--
ALTER TABLE `candidat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidat_messages_candidat_id_lu_index` (`candidat_id`,`lu`);

--
-- Index pour la table `conjoints`
--
ALTER TABLE `conjoints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conjoints_lieu_naissance_id_foreign` (`lieu_naissance_id`),
  ADD KEY `conjoints_nationalite_id_foreign` (`nationalite_id`),
  ADD KEY `conjoints_dignitaire_id_index` (`dignitaire_id`),
  ADD KEY `conjoints_statut_index` (`statut`),
  ADD KEY `conjoints_est_militaire_est_dignitaire_index` (`est_militaire`,`est_dignitaire`);

--
-- Index pour la table `decoration`
--
ALTER TABLE `decoration`
  ADD PRIMARY KEY (`deco_id`);

--
-- Index pour la table `decoration_dignitaire`
--
ALTER TABLE `decoration_dignitaire`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_decoration_dignitaire_dignitaire` (`dignitaire_id`),
  ADD KEY `fk_decoration_dignitaire_decoration` (`decoration_id`);

--
-- Index pour la table `dignitaire`
--
ALTER TABLE `dignitaire`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD KEY `fk_dignitaire_lieu_naissance` (`lieu_naissance`);

--
-- Index pour la table `dignitaire_documents`
--
ALTER TABLE `dignitaire_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dignitaire_documents_dignitaire_id_index` (`dignitaire_id`),
  ADD KEY `dignitaire_documents_type_document_index` (`type_document`),
  ADD KEY `dignitaire_documents_date_expiration_index` (`date_expiration`);

--
-- Index pour la table `dignitaire_emails`
--
ALTER TABLE `dignitaire_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dignitaire_id` (`dignitaire_id`);

--
-- Index pour la table `dignitaire_telephones`
--
ALTER TABLE `dignitaire_telephones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dignitaire_telephones_dignitaire_id_foreign` (`dignitaire_id`);

--
-- Index pour la table `diplome`
--
ALTER TABLE `diplome`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_diplome_dignitaire` (`dignitaire_id`),
  ADD KEY `fk_diplome_etablissement` (`etablissement_id`),
  ADD KEY `fk_diplome_ville` (`ville_id`),
  ADD KEY `fk_diplome_domaine` (`domaine_id`);

--
-- Index pour la table `domaine`
--
ALTER TABLE `domaine`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `enfants`
--
ALTER TABLE `enfants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_enfant_dignitaire` (`dignitaire_id`),
  ADD KEY `fk_enfant_lieu_naissance` (`lieu_naissance`);

--
-- Index pour la table `entite`
--
ALTER TABLE `entite`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `etablissement`
--
ALTER TABLE `etablissement`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD KEY `fk_etablissement_ville` (`ville_id`);

--
-- Index pour la table `experiences`
--
ALTER TABLE `experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_experience_dignitaire` (`dignitaire_id`),
  ADD KEY `fk_experience_structure` (`structure_id`);

--
-- Index pour la table `fonctions`
--
ALTER TABLE `fonctions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fonction_name` (`fonction_name`);

--
-- Index pour la table `historique_nominations`
--
ALTER TABLE `historique_nominations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_historique_nomination_nomination` (`nomination_id`),
  ADD KEY `fk_historique_nomination_dignitaire` (`dignitaire_id`),
  ADD KEY `fk_historique_nomination_poste` (`poste_id`),
  ADD KEY `fk_historique_nomination_entite` (`entite_id`);

--
-- Index pour la table `langue`
--
ALTER TABLE `langue`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `langues`
--
ALTER TABLE `langues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_langue_dignitaire` (`dignitaire_id`),
  ADD KEY `fk_langue_code` (`langue_id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `nominations`
--
ALTER TABLE `nominations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_nomination_dignitaire` (`dignitaire_id`),
  ADD KEY `fk_nomination_entite` (`entite_id`),
  ADD KEY `fk_nomination_poste` (`poste_id`),
  ADD KEY `fk_nomination_pv` (`pv_id`);

--
-- Index pour la table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `otp_codes_email_type_purpose_index` (`email`,`type`,`purpose`);

--
-- Index pour la table `parametres`
--
ALTER TABLE `parametres`
  ADD PRIMARY KEY (`cle`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`,`type`);

--
-- Index pour la table `pays`
--
ALTER TABLE `pays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD UNIQUE KEY `code_iso` (`code_iso`),
  ADD KEY `fk_pays_region` (`region_id`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `postes`
--
ALTER TABLE `postes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_postes_dignitaire` (`dignitaire_id`),
  ADD KEY `fk_postes_entite` (`entite_id`),
  ADD KEY `fk_postes_ville` (`ville_id`);

--
-- Index pour la table `pv`
--
ALTER TABLE `pv`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`);

--
-- Index pour la table `rapports`
--
ALTER TABLE `rapports`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `region`
--
ALTER TABLE `region`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD KEY `region_continent_index` (`continent`),
  ADD KEY `region_type_index` (`type`),
  ADD KEY `region_pays_nom_index` (`pays_nom`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Index pour la table `roles_fonctions`
--
ALTER TABLE `roles_fonctions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_id` (`role_id`,`fonction_id`),
  ADD KEY `fonction_id` (`fonction_id`);

--
-- Index pour la table `roles_sousfonctions`
--
ALTER TABLE `roles_sousfonctions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_id` (`role_id`,`sousfonction_id`),
  ADD KEY `sousfonction_id` (`sousfonction_id`);

--
-- Index pour la table `sousfonctions`
--
ALTER TABLE `sousfonctions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fonction_id` (`fonction_id`);

--
-- Index pour la table `structure`
--
ALTER TABLE `structure`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD KEY `fk_structure_ville` (`ville_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Index pour la table `user_fonctions`
--
ALTER TABLE `user_fonctions`
  ADD PRIMARY KEY (`user_id`,`fonction_id`),
  ADD KEY `fonction_id` (`fonction_id`);

--
-- Index pour la table `user_sousfonctions`
--
ALTER TABLE `user_sousfonctions`
  ADD PRIMARY KEY (`user_id`,`sousfonction_id`),
  ADD KEY `sousfonction_id` (`sousfonction_id`);

--
-- Index pour la table `ville`
--
ALTER TABLE `ville`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pays_ville` (`pays_id`),
  ADD KEY `ville_region_id_index` (`region_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `affectations`
--
ALTER TABLE `affectations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT pour la table `candidats`
--
ALTER TABLE `candidats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `candidat_diplomes`
--
ALTER TABLE `candidat_diplomes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `candidat_documents`
--
ALTER TABLE `candidat_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `candidat_experiences`
--
ALTER TABLE `candidat_experiences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `candidat_langues`
--
ALTER TABLE `candidat_langues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `candidat_messages`
--
ALTER TABLE `candidat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `conjoints`
--
ALTER TABLE `conjoints`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `decoration`
--
ALTER TABLE `decoration`
  MODIFY `deco_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `decoration_dignitaire`
--
ALTER TABLE `decoration_dignitaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `dignitaire`
--
ALTER TABLE `dignitaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `dignitaire_documents`
--
ALTER TABLE `dignitaire_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `dignitaire_emails`
--
ALTER TABLE `dignitaire_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `dignitaire_telephones`
--
ALTER TABLE `dignitaire_telephones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `diplome`
--
ALTER TABLE `diplome`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `domaine`
--
ALTER TABLE `domaine`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `enfants`
--
ALTER TABLE `enfants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `entite`
--
ALTER TABLE `entite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `etablissement`
--
ALTER TABLE `etablissement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `experiences`
--
ALTER TABLE `experiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `fonctions`
--
ALTER TABLE `fonctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `historique_nominations`
--
ALTER TABLE `historique_nominations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `langue`
--
ALTER TABLE `langue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `langues`
--
ALTER TABLE `langues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `nominations`
--
ALTER TABLE `nominations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pays`
--
ALTER TABLE `pays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT pour la table `postes`
--
ALTER TABLE `postes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `pv`
--
ALTER TABLE `pv`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `rapports`
--
ALTER TABLE `rapports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `region`
--
ALTER TABLE `region`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=209;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `roles_fonctions`
--
ALTER TABLE `roles_fonctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `roles_sousfonctions`
--
ALTER TABLE `roles_sousfonctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `sousfonctions`
--
ALTER TABLE `sousfonctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `structure`
--
ALTER TABLE `structure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `ville`
--
ALTER TABLE `ville`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=587;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD CONSTRAINT `admin_notifications_candidat_id_foreign` FOREIGN KEY (`candidat_id`) REFERENCES `candidats` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `candidats`
--
ALTER TABLE `candidats`
  ADD CONSTRAINT `candidats_dignitaire_id_foreign` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `candidats_lieu_naissance_id_foreign` FOREIGN KEY (`lieu_naissance_id`) REFERENCES `ville` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `candidats_valide_par_foreign` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `candidats_ville_residence_id_foreign` FOREIGN KEY (`ville_residence_id`) REFERENCES `ville` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `candidat_diplomes`
--
ALTER TABLE `candidat_diplomes`
  ADD CONSTRAINT `candidat_diplomes_candidat_id_foreign` FOREIGN KEY (`candidat_id`) REFERENCES `candidats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidat_diplomes_domaine_id_foreign` FOREIGN KEY (`domaine_id`) REFERENCES `domaine` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `candidat_diplomes_etablissement_id_foreign` FOREIGN KEY (`etablissement_id`) REFERENCES `etablissement` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `candidat_diplomes_ville_id_foreign` FOREIGN KEY (`ville_id`) REFERENCES `ville` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `candidat_documents`
--
ALTER TABLE `candidat_documents`
  ADD CONSTRAINT `candidat_documents_candidat_id_foreign` FOREIGN KEY (`candidat_id`) REFERENCES `candidats` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `candidat_experiences`
--
ALTER TABLE `candidat_experiences`
  ADD CONSTRAINT `candidat_experiences_candidat_id_foreign` FOREIGN KEY (`candidat_id`) REFERENCES `candidats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidat_experiences_structure_id_foreign` FOREIGN KEY (`structure_id`) REFERENCES `structure` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `candidat_langues`
--
ALTER TABLE `candidat_langues`
  ADD CONSTRAINT `candidat_langues_candidat_id_foreign` FOREIGN KEY (`candidat_id`) REFERENCES `candidats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidat_langues_langue_id_foreign` FOREIGN KEY (`langue_id`) REFERENCES `langue` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `candidat_messages`
--
ALTER TABLE `candidat_messages`
  ADD CONSTRAINT `candidat_messages_candidat_id_foreign` FOREIGN KEY (`candidat_id`) REFERENCES `candidats` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `conjoints`
--
ALTER TABLE `conjoints`
  ADD CONSTRAINT `conjoints_dignitaire_id_foreign` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conjoints_lieu_naissance_id_foreign` FOREIGN KEY (`lieu_naissance_id`) REFERENCES `ville` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `conjoints_nationalite_id_foreign` FOREIGN KEY (`nationalite_id`) REFERENCES `pays` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `decoration_dignitaire`
--
ALTER TABLE `decoration_dignitaire`
  ADD CONSTRAINT `fk_decoration_dignitaire_decoration` FOREIGN KEY (`decoration_id`) REFERENCES `decoration` (`deco_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_decoration_dignitaire_dignitaire` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `dignitaire`
--
ALTER TABLE `dignitaire`
  ADD CONSTRAINT `fk_dignitaire_lieu_naissance` FOREIGN KEY (`lieu_naissance`) REFERENCES `ville` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `dignitaire_documents`
--
ALTER TABLE `dignitaire_documents`
  ADD CONSTRAINT `dignitaire_documents_dignitaire_id_foreign` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `dignitaire_emails`
--
ALTER TABLE `dignitaire_emails`
  ADD CONSTRAINT `dignitaire_emails_dignitaire_id_foreign` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `dignitaire_telephones`
--
ALTER TABLE `dignitaire_telephones`
  ADD CONSTRAINT `dignitaire_telephones_dignitaire_id_foreign` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `diplome`
--
ALTER TABLE `diplome`
  ADD CONSTRAINT `fk_diplome_dignitaire` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_diplome_domaine` FOREIGN KEY (`domaine_id`) REFERENCES `domaine` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_diplome_etablissement` FOREIGN KEY (`etablissement_id`) REFERENCES `etablissement` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_diplome_ville` FOREIGN KEY (`ville_id`) REFERENCES `ville` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `enfants`
--
ALTER TABLE `enfants`
  ADD CONSTRAINT `fk_enfant_dignitaire` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enfant_lieu_naissance` FOREIGN KEY (`lieu_naissance`) REFERENCES `ville` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `etablissement`
--
ALTER TABLE `etablissement`
  ADD CONSTRAINT `fk_etablissement_ville` FOREIGN KEY (`ville_id`) REFERENCES `ville` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `experiences`
--
ALTER TABLE `experiences`
  ADD CONSTRAINT `fk_experience_dignitaire` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_experience_structure` FOREIGN KEY (`structure_id`) REFERENCES `structure` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `historique_nominations`
--
ALTER TABLE `historique_nominations`
  ADD CONSTRAINT `fk_historique_nomination_dignitaire` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_historique_nomination_entite` FOREIGN KEY (`entite_id`) REFERENCES `entite` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_historique_nomination_nomination` FOREIGN KEY (`nomination_id`) REFERENCES `nominations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_historique_nomination_poste` FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `langues`
--
ALTER TABLE `langues`
  ADD CONSTRAINT `fk_langue_code` FOREIGN KEY (`langue_id`) REFERENCES `langue` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_langue_dignitaire` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `nominations`
--
ALTER TABLE `nominations`
  ADD CONSTRAINT `fk_nomination_dignitaire` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_nomination_entite` FOREIGN KEY (`entite_id`) REFERENCES `entite` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_nomination_poste` FOREIGN KEY (`poste_id`) REFERENCES `postes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_nomination_pv` FOREIGN KEY (`pv_id`) REFERENCES `pv` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `pays`
--
ALTER TABLE `pays`
  ADD CONSTRAINT `fk_pays_region` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `postes`
--
ALTER TABLE `postes`
  ADD CONSTRAINT `fk_postes_dignitaire` FOREIGN KEY (`dignitaire_id`) REFERENCES `dignitaire` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_postes_entite` FOREIGN KEY (`entite_id`) REFERENCES `entite` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_postes_ville` FOREIGN KEY (`ville_id`) REFERENCES `ville` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `roles_fonctions`
--
ALTER TABLE `roles_fonctions`
  ADD CONSTRAINT `roles_fonctions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_fonctions_ibfk_2` FOREIGN KEY (`fonction_id`) REFERENCES `fonctions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `roles_sousfonctions`
--
ALTER TABLE `roles_sousfonctions`
  ADD CONSTRAINT `roles_sousfonctions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_sousfonctions_ibfk_2` FOREIGN KEY (`sousfonction_id`) REFERENCES `sousfonctions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sousfonctions`
--
ALTER TABLE `sousfonctions`
  ADD CONSTRAINT `sousfonctions_ibfk_1` FOREIGN KEY (`fonction_id`) REFERENCES `fonctions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `structure`
--
ALTER TABLE `structure`
  ADD CONSTRAINT `fk_structure_ville` FOREIGN KEY (`ville_id`) REFERENCES `ville` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_fonctions`
--
ALTER TABLE `user_fonctions`
  ADD CONSTRAINT `user_fonctions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_fonctions_ibfk_2` FOREIGN KEY (`fonction_id`) REFERENCES `fonctions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_sousfonctions`
--
ALTER TABLE `user_sousfonctions`
  ADD CONSTRAINT `user_sousfonctions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_sousfonctions_ibfk_2` FOREIGN KEY (`sousfonction_id`) REFERENCES `sousfonctions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ville`
--
ALTER TABLE `ville`
  ADD CONSTRAINT `fk_pays_ville` FOREIGN KEY (`pays_id`) REFERENCES `pays` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ville_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `region` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
