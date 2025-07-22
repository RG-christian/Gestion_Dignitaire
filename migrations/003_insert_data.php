<?php
require_once __DIR__ . '/../config/database.php';
$pdo = getDatabaseConnection();

/* ===================== BLOCS SQL (inchangés sauf corrections indiquées) ===================== */

/* 1. Rôles & droits */
$sql_data_roles = "
INSERT INTO roles (id, role_name) VALUES
(1,'Superadmin'),
(2,'Assistant');
";


$sql_data_fonctions = "
INSERT INTO fonctions (id, fonction_name) VALUES
(1,'Gest. Pers.'),
(2,'Éduc. & Qualif.'),
(3,'Parcours Pro.'),
(4,'Langues'),
(5,'Géographie'),
(6,'Récomp. & Rec.'),
(7,'organisation');
";

$sql_data_sous_fonctions = "
INSERT INTO sousfonctions (id, sousfonction_name, fonction_id) VALUES
(1, 'Enfant',       1),
(2, 'Dignitaire',   1),
(3, 'Poste',        1),
(4, 'Diplôme',      2),
(5, 'Expérience',   3),
(6, 'Langues',      4),
(7, 'Pays/ville',   5),
(8, 'Nomination',   6),
(9, 'Décoration',   6),
(10,'Structure',    7);
";

$sql_data_users = "
INSERT INTO users (id, username, nom_complet, password, email, role_id, created_at) VALUES
(9,  'Magali', 'magali', '\$2y\$10\$AYXmwscOlXPYVAoh2C7Cwu6el6jBXWnsXYQucOjN4Gj/4uXTA3kYG', 'devgroupentreprise@gmail.com', 2, '2025-07-18 23:54:39'),
(11, 'admin1', 'admin1', '\$2y\$10\$ZHJ8pOuKtsFvGwWb8HcZyOtlVtgKaBm7xYOOpA7ql.K9FRlL3oGjS', 'astiger4@gmail.com', 1, '2025-07-19 00:39:54'),
(12, 'dorkas', 'Akanda', '\$2y\$10\$kAwP44FRid.N78l7yg0SkObRW7JIMlqeeEfW2IUrBtlZLp0TialrG', 'georgeschristian2202@gmail.com', 2, '2025-07-19 00:56:44'),
(14, 'Magali dorkas Akanda rut', 'grace', '\$2y\$10\$EDQYWuyoJwsfCWaJLLnyBOVfiC0KBhkAux0/O7Ei8L4jO0C4/w24i', 'rapontchombogeorges22@gmail.com', 2, '2025-07-19 01:22:26'),
(16, 'tito', 'tito', '\$2y\$10\$wvdzxV7QOUxEFsNvK2lgg.V7n/qP03NFDIYlsElOfkkvN3dTt8wCa', 'tito@gmail.com', 1, '2025-07-19 02:34:00');
";

$sql_data_role_fonction ="
INSERT INTO roles_fonctions (role_id, fonction_id) VALUES
(1, 1),(1, 2),(1, 3),(1, 4),(1, 5),(1, 6),(1, 7),
(2, 1);
";

$sql_data_roles_sousfonctions ="
INSERT INTO roles_sousfonctions (role_id, sousfonction_id) VALUES
(1, 1),(1, 2),(1, 3),(1, 4),(1, 5),(1, 6),(1, 7),(1, 8),(1, 9),(1,10),
(2, 1);
";

$sql_data_user_fonctions = "
INSERT INTO user_fonctions (user_id, fonction_id) VALUES
(9,1),(11,1),(12,1),(16,1),
(11,2),(14,2),
(11,3),
(11,4),
(11,5),(16,5),
(11,6),
(11,7);
";

$sql_data_user_sousfonctions = "
INSERT INTO user_sousfonctions (user_id, sousfonction_id) VALUES
(11,1),(12,1),
(9,2),(11,2),(16,2),
(11,3),
(11,4),(14,4),
(11,5),
(11,6),
(11,7),(16,7),
(11,8),
(11,9),
(11,10);
";

/* 2. Référentiels & structures */
$sql_data_domaine = "
INSERT INTO domaine (id, nom) VALUES
(1,'Sciences Politiques'),
(2,'Droit'),
(3,'Économie'),
(4,'Administration Publique'),
(5,'Informatique'),
(6,'Lettres Modernes'),
(7,'Gestion');
";

$sql_data_langue = "
INSERT INTO langue (id, nom) VALUES
(1,'Français'),
(2,'Anglais'),
(3,'Espagnol'),
(4,'Fang'),
(5,'Myéné'),
(6,'Punu'),
(7,'Nzebi');
";

$sql_data_region = "
INSERT INTO region (id, nom) VALUES
(5,'Afrique australe'),
(3,'Afrique centrale'),
(4,'Afrique de l’Est'),
(2,'Afrique de l’Ouest'),
(1,'Afrique du Nord'),
(15,'Amérique centrale'),
(14,'Amérique du Nord'),
(16,'Amérique du Sud'),
(9,'Asie centrale'),
(12,'Asie du Sud'),
(11,'Asie du Sud-Est'),
(13,'Asie orientale'),
(17,'Caraïbes'),
(7,'Europe centrale et orientale'),
(6,'Europe de l’Ouest'),
(8,'Europe du Sud'),
(10,'Moyen-Orient'),
(18,'Océanie');
";

$sql_data_pays = "
INSERT INTO pays (id, nom, code_iso, indicatif, continent, region_id) VALUES
(1,'Afrique du Sud','ZA','+27','Afrique',5),
(2,'Algérie','DZ','+213','Afrique',1),
(3,'Angola','AO','+244','Afrique',5),
(4,'Bénin','BJ','+229','Afrique',2),
(5,'Cameroun','CM','+237','Afrique',3),
(6,'Congo Brazaville','CG','+242','Afrique',3),
(7,'RD Congo','CD','+243','Afrique',3),
(8,'Côte dIvoire','CI','+225','Afrique',2),
(9,'Égypte','EG','+20','Afrique',1),
(10,'Éthiopie','ET','+251','Afrique',4),
(11,'Guinée équatoriale','GQ','+240','Afrique',3),
(12,'Libye','LY','+218','Afrique',1),
(13,'Mali','ML','+223','Afrique',2),
(14,'Maroc','MA','+212','Afrique',1),
(15,'Nigeria','NG','+234','Afrique',2),
(16,'Sao Tomé-et-Principe','ST','+239','Afrique',3),
(17,'Sénégal','SN','+221','Afrique',2),
(18,'Togo','TG','+228','Afrique',2),
(19,'Tunisie','TN','+216','Afrique',1),
(20,'Brésil','BR','+55','Amérique',16),
(21,'Canada','CA','+1','Amérique',14),
(22,'Cuba','CU','+53','Amérique',17),
(23,'États-Unis','US','+1','Amérique',14),
(24,'Arabie saoudite','SA','+966','Asie',10),
(25,'Chine','CN','+86','Asie',9),
(26,'Corée du Sud','KR','+82','Asie',13),
(27,'Inde','IN','+91','Asie',12),
(28,'Japon','JP','+81','Asie',13),
(29,'Liban','LB','+961','Asie',10),
(30,'Turquie','TR','+90','Asie',8),
(31,'Allemagne','DE','+49','Europe',6),
(32,'Belgique','BE','+32','Europe',6),
(33,'Espagne','ES','+34','Europe',8),
(34,'France','FR','+33','Europe',6),
(35,'Italie','IT','+39','Europe',8),
(36,'Royaume-Uni','GB','+44','Europe',6),
(37,'Russie','RU','+7','Europe',7),
(38,'Vatican','VA','+379','Europe',8),
(39,'République centrafricaine','CF','+236','Afrique',3),
(40,'Gabon','GA','+241','Afrique',3);
";

$sql_data_ville = "
INSERT INTO ville (id, nom, pays_id) VALUES
(1,'Pretoria',1),
(2,'Alger',2),
(3,'Luanda',3),
(4,'Cotonou',4),
(5,'Yaoundé',5),
(6,'Brazzaville',6),
(7,'Kinshasa',7),
(8,'Abidjan',8),
(9,'Le Caire',9),
(10,'Addis Ababa',10),
(11,'Malabo',11),
(12,'Bata',11),
(13,'Tripoli',12),
(14,'Bamako',13),
(15,'Rabat',14),
(16,'Abuja',15),
(17,'São Tomé',16),
(18,'Dakar',17),
(19,'Lomé',18),
(20,'Tunis',19),
(21,'Brasília',20),
(22,'Ottawa',21),
(23,'La Havane',22),
(24,'Washington',23),
(25,'Riyad',24),
(26,'Pékin',25),
(27,'Séoul',26),
(28,'New Delhi',27),
(29,'Tokyo',28),
(30,'Beyrouth',29),
(31,'Ankara',30),
(32,'Berlin',31),
(33,'Bruxelles',32),
(34,'Madrid',33),
(35,'Paris',34),
(36,'Rome',35),
(37,'Londres',36),
(38,'Moscou',37),
(39,'Rome',38),
(40,'Bangui',39);
";

$sql_data_structure = "
INSERT INTO structure (id, nom) VALUES
(1,'Université Omar Bongo'),
(2,'Total Gabon'),
(3,'Banque des États de l’Afrique Centrale'),
(4,'Port Autonome de Libreville'),
(5,'Ministère des Finances'),
(6,'Hôpital d’Instruction des Armées'),
(7,'Société Gabonaise de Transport');
";

$sql_data_etablissement = "
INSERT INTO etablissement (id, nom, type, ville_id) VALUES
(1,'Université Omar Bongo','Université',1),
(2,'Lycée National Léon Mba','Lycée',1),
(3,'Institut National des Sciences','Institut',2),
(4,'École Nationale d’Administration','École',1),
(5,'Université des Sciences de la Santé','Université',1);
";

$sql_data_entites = "
INSERT INTO entite (id, nom, id_sup) VALUES
(1,'Présidence de la République',NULL),
(2,'Ministère de la Défense',NULL),
(3,'Ministère de l’Intérieur',NULL),
(4,'Assemblée Nationale',NULL),
(5,'Sénat',NULL),
(6,'Ambassade du Gabon en France',NULL),
(7,'Conseil National de la Communication',NULL);
";

$sql_data_decoration = "
INSERT INTO decoration (deco_id, deco_nom, deco_type, deco_niveau, deco_grade, deco_date_obtention, deco_autorite, deco_motif, deco_description, deco_fichierAttestation) VALUES
(1,'Ordre National du Mérite','National','Or','Grand Officier','2010-05-01','Président','Service rendu','Décoration nationale pour service exceptionnel','attestation1.pdf'),
(2,'Médaille du Travail','Professionnel','Argent','Officier','2015-09-15','Ministre du Travail','Ancienneté','Récompense pour 30 ans de service','attestation2.pdf');
";


$sql_data_pv = "
INSERT INTO pv (numero, date, description) VALUES
('PV2025-001','2025-01-15','Procès-verbal de nomination'),
('PV2025-002','2025-03-10','Procès-verbal de réunion du conseil');
";

/* 3. Table principale */
$sql_data_dignitaire= "
INSERT INTO dignitaire (id, nip, matricule, nom, prenom, date_naissance, nationalite, lieu_naissance, genre, etat_civil, telephone, adresse, photo, casierJud, certificatsMed) VALUES
(1,'NIP001','MAT001','BONGO ','Ali','1959-02-09',NULL,1,'Homme','Marié',NULL,NULL,'image1.png',NULL,NULL),
(2,'NIP002','MAT002','ONDO','Rose','1965-05-12',NULL,2,'Femme','Veuve',NULL,NULL,'image2.png',NULL,NULL),
(3,'NIP003','MAT003','NDONG','Paul','1971-09-17',NULL,3,'Homme','Célibataire',NULL,NULL,'image3.png',NULL,NULL),
(4,'NIP004','MAT004','MOUSSA','Fatou','1968-11-23',NULL,4,'Femme','Mariée',NULL,NULL,'image1.png',NULL,NULL),
(5,'NIP005','MAT005','MEYE ','Serge','1975-03-30',NULL,1,'Homme','divorcé',NULL,NULL,'image2.png',NULL,NULL),
(6,'NIP006','MAT006','NDONG','Raymond','1970-05-15','Gabonaise',1,'Homme','Marié(e)','0712345678','Libreville','image3.png',NULL,NULL),
(7,'NIP007','MAT007','BOUMBA','Clarisse','1975-08-20','Gabonaise',2,'Femme','Célibataire','0623456789','Port-Gentil','image1.png',NULL,NULL),
(8,'NIP008','MAT008','MABICKA','Jean-Paul','1965-03-10','Gabonaise',3,'Homme','Marié(e)','0777777777','Franceville','image2.png',NULL,NULL),
(9,'NIP009','MAT009','NTOUTOUME','Agnès','1980-09-12','Gabonaise',4,'Femme','Marié(e)','0611223344','Lambaréné','image3.png',NULL,NULL),
(10,'NIP010','MAT0010','OKOME','Franck','1982-11-23','Gabonaise',5,'Homme','Célibataire','0666778899','Oyem','image1.png',NULL,NULL),
(11,'NIP0011','MAT0011','MBOUMBA','Georgette','1978-02-28','Gabonaise',1,'Femme','Veuve','0755566666','Mouila','image2.png',NULL,NULL),
(12,'NIP0012','MAT0012','BONGO','Albert','1955-07-30','Gabonaise',2,'Homme','Marié(e)','0600001122','Libreville','image3.png',NULL,NULL),
(13,'NIP0013','MAT0013','MBINA','Rose','1985-01-05','Gabonaise',3,'Femme','Célibataire','0688996655','Port-Gentil','image1.png',NULL,NULL),
(14,'NIP0014','MAT0014','KOUMBA','Richard','1972-06-17','Gabonaise',4,'Homme','Divorcé(e)','0701010101','Franceville','image2.png',NULL,NULL),
(15,'NIP015','MAT015','NGUEMA','Sylvia','1990-12-11','Gabonaise',5,'Femme','Célibataire','0696969696','Bitam','image1.png',NULL,NULL);
";

/* 4. Tables dépendantes */
$sql_data_diplome = "
INSERT INTO diplome (id, dignitaire_id, intitule, etablissement_id, annee, ville_id, domaine_id, code, type) VALUES
(1,1,'Doctorat en Droit',1,'1990',1,2,'DOC001','Doctorat'),
(2,2,'Master Sciences Politiques',2,'1992',2,1,'MAS001','Master'),
(3,3,'Licence Informatique',3,'1995',3,5,'LIC001','Licence'),
(4,4,'DESS Gestion',4,'1993',4,7,'DESS001','DESS'),
(5,5,'CAPES Lettres Modernes',5,'1991',1,6,'CAP001','CAPES');
";

$sql_data_enfants = "
INSERT INTO enfants (id, nom, prenom, date_naissance, lieu_naissance, genre, dignitaire_id) VALUES
(1,'BONGO','Junior','1990-07-14',1,'Homme',1),
(2,'BONGO','Sylvia','1994-01-22',2,'Femme',1),
(3,'ONDO','Patrick','1989-05-12',2,'Homme',2),
(4,'MOUSSA','Mariama','2002-08-05',4,'Femme',3),
(5,'MEYE','Nicolas','2003-12-20',1,'Homme',4);
";

$sql_data_langues = "
INSERT INTO langues (id, dignitaire_id, langue_id, niveau) VALUES
(1,1,1,'Courant'),
(2,1,2,'Moyen'),
(3,2,1,'Courant'),
(4,2,4,'Débutant'),
(5,3,5,'Bilingue');
";

$sql_data_experiences = "
INSERT INTO experiences (id, dignitaire_id, intitule, date_debut, date_fin, structure_id) VALUES
(1,1,'Avocat à la Cour','1985-01-01','1990-12-31',1),
(2,2,'Directrice Cabinet Ministériel','2000-04-10','2005-09-15',5),
(3,3,'Ingénieur Systèmes','2002-05-05','2010-07-30',2),
(4,4,'Chargée de Mission','2007-03-01','2014-02-28',3),
(5,5,'Chef de Service','2011-11-01','2016-06-30',4);
";

$sql_data_postes = "
INSERT INTO postes (id, dignitaire_id, intitule, date_debut, date_fin, entite_id, ville_id) VALUES
(1,1,'Président de la République','2009-10-16','2025-04-12',1,1),
(2,2,'Ministre de l''Intérieur','2010-03-20','2016-08-05',2,2),
(3,3,'Député','2014-02-12',NULL,4,3),
(4,4,'Ambassadrice','2017-09-08','2022-06-30',6,4),
(5,5,'Sénateur','2015-07-01','2021-09-25',5,4),
(6,6,'Directeur Général','2016-01-15',NULL,2,2),
(7,7,'Secrétaire Général','2018-10-01',NULL,2,5),
(8,8,'Chef de Service','2008-03-17','2017-04-20',2,1),
(9,9,'Conseiller Spécial','2019-06-11','2023-01-01',1,6),
(10,10,'Directeur de Cabinet','2012-10-10',NULL,7,7),
(11,11,'Gouverneure','2015-06-14','2022-02-10',7,2),
(12,12,'Attachée de Presse','2022-01-20','2011-11-30',6,3),
(13,13,'Conseillère Diplomatique','2021-02-18',NULL,1,4),
(14,14,'Directeur de Finances','2010-02-22','2017-07-15',1,6),
(15,15,'Présidente du Sénat','2020-05-14',NULL,7,7);
";

$sql_data_nominations = "
INSERT INTO nominations
(id, dignitaire_id, entite_id, poste_id, pv_id, date_debut, date_fin, fonction)
VALUES
(1,1,1,NULL,NULL,'2000-01-01','2005-12-31','Président de la République'),
(2,2,2,NULL,NULL,'2005-01-01','2010-12-31','Ministre de la Défense'),
(3,3,3,NULL,NULL,'2010-01-01','2015-12-31','Ministre de l’Intérieur'),
(4,4,4,NULL,NULL,'2015-01-01','2020-12-31','Présidente de l’Assemblée Nationale'),
(5,5,5,NULL,NULL,'2020-01-01',NULL,'Sénateur');
";

$sql_data_historique_nominations = "
INSERT INTO historique_nominations (nomination_id, dignitaire_id, poste_id, entite_id, date_nomination, date_fin, description) VALUES
(1,1,1,1,'2000-01-01','2005-12-31','Premier mandat'),
(2,2,2,2,'2005-01-01','2010-12-31','Ministre de la Défense');
";

$sql_data_decoration_dignitaire = "
INSERT INTO decoration_dignitaire (dignitaire_id, decoration_id, date_attribution) VALUES
(1,1,'2010-05-01'),
(2,2,'2015-09-15');
";


/* ===================== EXECUTION ===================== */
try {
    $pdo->beginTransaction();

    // 1. Rôles & droits
    $pdo->exec($sql_data_roles);
    $pdo->exec($sql_data_fonctions);
    $pdo->exec($sql_data_sous_fonctions);
    $pdo->exec($sql_data_users);
    $pdo->exec($sql_data_role_fonction);
    $pdo->exec($sql_data_roles_sousfonctions);
    $pdo->exec($sql_data_user_fonctions);
    $pdo->exec($sql_data_user_sousfonctions);

    // 2. Référentiels / structures / entités / décorations / PV
    $pdo->exec($sql_data_domaine);
    $pdo->exec($sql_data_langue);
    $pdo->exec($sql_data_region);
    $pdo->exec($sql_data_pays);
    $pdo->exec($sql_data_ville);
    $pdo->exec($sql_data_structure);
    $pdo->exec($sql_data_etablissement);
    $pdo->exec($sql_data_entites);
    $pdo->exec($sql_data_decoration);
    $pdo->exec($sql_data_pv);

    // 3. Table principale
    $pdo->exec($sql_data_dignitaire);

    // 4. Dépendants
    $pdo->exec($sql_data_diplome);
    $pdo->exec($sql_data_enfants);
    $pdo->exec($sql_data_langues);
    $pdo->exec($sql_data_experiences);
    $pdo->exec($sql_data_postes);
    $pdo->exec($sql_data_nominations);
    $pdo->exec($sql_data_historique_nominations);
    $pdo->exec($sql_data_decoration_dignitaire);

    $pdo->commit();
    echo "Toutes les insertions ont été exécutées avec succès.\n";

} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Erreur lors des insertions : " . $e->getMessage() . "\n";
}
