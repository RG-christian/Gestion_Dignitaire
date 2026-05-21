-- Créer les tables pour téléphones et emails
CREATE TABLE IF NOT EXISTS `dignitaire_telephones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dignitaire_id` bigint unsigned NOT NULL,
  `numero` varchar(20) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'mobile',
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dignitaire_telephones_dignitaire_id_index` (`dignitaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `dignitaire_emails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `dignitaire_id` bigint unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'personnel',
  `principal` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dignitaire_emails_dignitaire_id_index` (`dignitaire_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
