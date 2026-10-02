-- REINITIALISATION LOCALE UNIQUEMENT
-- Attention : ce script efface entièrement la base gestion_dignitaire.
-- Le dump source gestion_dignitaire.sql est conservé intact.

DROP DATABASE IF EXISTS gestion_dignitaire;
CREATE DATABASE gestion_dignitaire
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE gestion_dignitaire;

-- Charge l'export existant. Les slashs sont intentionnels pour le client MySQL Windows.
SOURCE C:/Users/Georges RAPONTCHOMBO/MyWorkspace/projet-web-2026/Gestion_Dignitaire-1/gestion-dignitaire-v2/backup-sql/gestion_dignitaire.sql;
