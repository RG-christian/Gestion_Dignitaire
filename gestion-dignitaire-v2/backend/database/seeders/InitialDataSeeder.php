<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. ROLES
        DB::table('roles')->insert([
            ['id' => 1, 'role_name' => 'Superadmin'],
            ['id' => 2, 'role_name' => 'Assistant'],
        ]);

        // 2. FONCTIONS
        DB::table('fonctions')->insert([
            ['id' => 1, 'fonction_name' => 'Gest. Pers.'],
            ['id' => 2, 'fonction_name' => 'Éduc. & Qualif.'],
            ['id' => 3, 'fonction_name' => 'Parcours Pro.'],
            ['id' => 4, 'fonction_name' => 'Langues'],
            ['id' => 5, 'fonction_name' => 'Géographie'],
            ['id' => 6, 'fonction_name' => 'Récomp. & Rec.'],
            ['id' => 7, 'fonction_name' => 'organisation'],
        ]);

        // 3. SOUS-FONCTIONS
        DB::table('sousfonctions')->insert([
            ['id' => 1, 'sousfonction_name' => 'Enfant', 'fonction_id' => 1],
            ['id' => 2, 'sousfonction_name' => 'Dignitaire', 'fonction_id' => 1],
            ['id' => 3, 'sousfonction_name' => 'Poste', 'fonction_id' => 1],
            ['id' => 4, 'sousfonction_name' => 'Diplôme', 'fonction_id' => 2],
            ['id' => 5, 'sousfonction_name' => 'Expérience', 'fonction_id' => 3],
            ['id' => 6, 'sousfonction_name' => 'Langues', 'fonction_id' => 4],
            ['id' => 7, 'sousfonction_name' => 'Pays', 'fonction_id' => 5],
            ['id' => 8, 'sousfonction_name' => 'Ville', 'fonction_id' => 5],
            ['id' => 9, 'sousfonction_name' => 'Nomination', 'fonction_id' => 6],
            ['id' => 10, 'sousfonction_name' => 'Décoration', 'fonction_id' => 6],
            ['id' => 11, 'sousfonction_name' => 'Structure', 'fonction_id' => 7],
        ]);

        // 4. USERS
        DB::table('users')->insert([
            ['id' => 9, 'username' => 'Magali', 'nom_complet' => 'magali', 'password' => '$2y$10$AYXmwscOlXPYVAoh2C7Cwu6el6jBXWnsXYQucOjN4Gj/4uXTA3kYG', 'email' => 'devgroupentreprise@gmail.com', 'role_id' => 2, 'created_at' => '2025-07-18 23:54:39'],
            ['id' => 11, 'username' => 'admin1', 'nom_complet' => 'admin1', 'password' => '$2y$10$ZHJ8pOuKtsFvGwWb8HcZyOtlVtgKaBm7xYOOpA7ql.K9FRlL3oGjS', 'email' => 'astiger4@gmail.com', 'role_id' => 1, 'created_at' => '2025-07-19 00:39:54'],
            ['id' => 12, 'username' => 'dorkas', 'nom_complet' => 'Akanda', 'password' => '$2y$10$kAwP44FRid.N78l7yg0SkObRW7JIMlqeeEfW2IUrBtlZLp0TialrG', 'email' => 'georgeschristian2202@gmail.com', 'role_id' => 2, 'created_at' => '2025-07-19 00:56:44'],
            ['id' => 14, 'username' => 'Magali dorkas Akanda rut', 'nom_complet' => 'grace', 'password' => '$2y$10$EDQYWuyoJwsfCWaJLLnyBOVfiC0KBhkAux0/O7Ei8L4jO0C4/w24i', 'email' => 'rapontchombogeorges22@gmail.com', 'role_id' => 2, 'created_at' => '2025-07-19 01:22:26'],
            ['id' => 16, 'username' => 'tito', 'nom_complet' => 'tito', 'password' => '$2y$10$wvdzxV7QOUxEFsNvK2lgg.V7n/qP03NFDIYlsElOfkkvN3dTt8wCa', 'email' => 'tito@gmail.com', 'role_id' => 1, 'created_at' => '2025-07-19 02:34:00'],
        ]);

        // 5. ROLES_FONCTIONS
        DB::table('roles_fonctions')->insert([
            ['role_id' => 1, 'fonction_id' => 1],
            ['role_id' => 1, 'fonction_id' => 2],
            ['role_id' => 1, 'fonction_id' => 3],
            ['role_id' => 1, 'fonction_id' => 4],
            ['role_id' => 1, 'fonction_id' => 5],
            ['role_id' => 1, 'fonction_id' => 6],
            ['role_id' => 1, 'fonction_id' => 7],
            ['role_id' => 2, 'fonction_id' => 1],
        ]);

        // 6. ROLES_SOUSFONCTIONS
        DB::table('roles_sousfonctions')->insert([
            ['role_id' => 1, 'sousfonction_id' => 1],
            ['role_id' => 1, 'sousfonction_id' => 2],
            ['role_id' => 1, 'sousfonction_id' => 3],
            ['role_id' => 1, 'sousfonction_id' => 4],
            ['role_id' => 1, 'sousfonction_id' => 5],
            ['role_id' => 1, 'sousfonction_id' => 6],
            ['role_id' => 1, 'sousfonction_id' => 7],
            ['role_id' => 1, 'sousfonction_id' => 8],
            ['role_id' => 1, 'sousfonction_id' => 9],
            ['role_id' => 1, 'sousfonction_id' => 10],
            ['role_id' => 2, 'sousfonction_id' => 1],
        ]);

        // 7. USER_FONCTIONS
        DB::table('user_fonctions')->insert([
            ['user_id' => 9, 'fonction_id' => 1],
            ['user_id' => 11, 'fonction_id' => 1],
            ['user_id' => 12, 'fonction_id' => 1],
            ['user_id' => 16, 'fonction_id' => 1],
            ['user_id' => 11, 'fonction_id' => 2],
            ['user_id' => 14, 'fonction_id' => 2],
            ['user_id' => 11, 'fonction_id' => 3],
            ['user_id' => 11, 'fonction_id' => 4],
            ['user_id' => 11, 'fonction_id' => 5],
            ['user_id' => 16, 'fonction_id' => 5],
            ['user_id' => 11, 'fonction_id' => 6],
            ['user_id' => 11, 'fonction_id' => 7],
        ]);

        // 8. USER_SOUSFONCTIONS
        DB::table('user_sousfonctions')->insert([
            ['user_id' => 11, 'sousfonction_id' => 1],
            ['user_id' => 12, 'sousfonction_id' => 1],
            ['user_id' => 9, 'sousfonction_id' => 2],
            ['user_id' => 11, 'sousfonction_id' => 2],
            ['user_id' => 16, 'sousfonction_id' => 2],
            ['user_id' => 11, 'sousfonction_id' => 3],
            ['user_id' => 11, 'sousfonction_id' => 4],
            ['user_id' => 14, 'sousfonction_id' => 4],
            ['user_id' => 11, 'sousfonction_id' => 5],
            ['user_id' => 11, 'sousfonction_id' => 6],
            ['user_id' => 11, 'sousfonction_id' => 7],
            ['user_id' => 16, 'sousfonction_id' => 7],
            ['user_id' => 11, 'sousfonction_id' => 8],
            ['user_id' => 11, 'sousfonction_id' => 9],
            ['user_id' => 11, 'sousfonction_id' => 10],
        ]);

        // 9. DOMAINE
        DB::table('domaine')->insert([
            ['id' => 1, 'nom' => 'Sciences Politiques'],
            ['id' => 2, 'nom' => 'Droit'],
            ['id' => 3, 'nom' => 'Économie'],
            ['id' => 4, 'nom' => 'Administration Publique'],
            ['id' => 5, 'nom' => 'Informatique'],
            ['id' => 6, 'nom' => 'Lettres Modernes'],
            ['id' => 7, 'nom' => 'Gestion'],
        ]);

        // 10. LANGUE
        DB::table('langue')->insert([
            ['id' => 1, 'nom' => 'Français'],
            ['id' => 2, 'nom' => 'Anglais'],
            ['id' => 3, 'nom' => 'Espagnol'],
            ['id' => 4, 'nom' => 'Fang'],
            ['id' => 5, 'nom' => 'Myéné'],
            ['id' => 6, 'nom' => 'Punu'],
            ['id' => 7, 'nom' => 'Nzebi'],
        ]);

        // 11. REGION
        DB::table('region')->insert([
            ['id' => 1, 'nom' => 'Afrique du Nord'],
            ['id' => 2, 'nom' => 'Afrique de l\'Ouest'],
            ['id' => 3, 'nom' => 'Afrique centrale'],
            ['id' => 4, 'nom' => 'Afrique de l\'Est'],
            ['id' => 5, 'nom' => 'Afrique australe'],
            ['id' => 6, 'nom' => 'Europe de l\'Ouest'],
            ['id' => 7, 'nom' => 'Europe centrale et orientale'],
            ['id' => 8, 'nom' => 'Europe du Sud'],
            ['id' => 9, 'nom' => 'Asie centrale'],
            ['id' => 10, 'nom' => 'Moyen-Orient'],
            ['id' => 11, 'nom' => 'Asie du Sud-Est'],
            ['id' => 12, 'nom' => 'Asie du Sud'],
            ['id' => 13, 'nom' => 'Asie orientale'],
            ['id' => 14, 'nom' => 'Amérique du Nord'],
            ['id' => 15, 'nom' => 'Amérique centrale'],
            ['id' => 16, 'nom' => 'Amérique du Sud'],
            ['id' => 17, 'nom' => 'Caraïbes'],
            ['id' => 18, 'nom' => 'Océanie'],
        ]);

        // 12. PAYS (seeder continuation dans un autre fichier si trop grand)
        $this->seedPays();
        
        // 13. VILLE
        $this->seedVille();
        
        // 14. STRUCTURE
        $this->seedStructure();
        
        // 15. ETABLISSEMENT
        $this->seedEtablissement();
        
        // 16. ENTITE
        $this->seedEntite();
        
        // 17. DECORATION
        $this->seedDecoration();
        
        // 18. PV
        $this->seedPv();
        
        // 19. DIGNITAIRE
        $this->seedDignitaire();
        
        // 20. Tables dépendantes
        $this->seedDiplome();
        $this->seedEnfants();
        $this->seedLangues();
        $this->seedExperiences();
        $this->seedPostes();
        $this->seedNominations();
        $this->seedHistoriqueNominations();
        $this->seedDecorationDignitaire();
    }

    private function seedPays()
    {
        $pays = [
            ['id' => 1, 'nom' => 'Afrique du Sud', 'code_iso' => 'ZA', 'indicatif' => '+27', 'continent' => 'Afrique', 'region_id' => 5],
            ['id' => 2, 'nom' => 'Algérie', 'code_iso' => 'DZ', 'indicatif' => '+213', 'continent' => 'Afrique', 'region_id' => 1],
            ['id' => 3, 'nom' => 'Angola', 'code_iso' => 'AO', 'indicatif' => '+244', 'continent' => 'Afrique', 'region_id' => 5],
            ['id' => 4, 'nom' => 'Bénin', 'code_iso' => 'BJ', 'indicatif' => '+229', 'continent' => 'Afrique', 'region_id' => 2],
            ['id' => 5, 'nom' => 'Cameroun', 'code_iso' => 'CM', 'indicatif' => '+237', 'continent' => 'Afrique', 'region_id' => 3],
            ['id' => 6, 'nom' => 'Congo Brazaville', 'code_iso' => 'CG', 'indicatif' => '+242', 'continent' => 'Afrique', 'region_id' => 3],
            ['id' => 7, 'nom' => 'RD Congo', 'code_iso' => 'CD', 'indicatif' => '+243', 'continent' => 'Afrique', 'region_id' => 3],
            ['id' => 8, 'nom' => 'Côte dIvoire', 'code_iso' => 'CI', 'indicatif' => '+225', 'continent' => 'Afrique', 'region_id' => 2],
            ['id' => 9, 'nom' => 'Égypte', 'code_iso' => 'EG', 'indicatif' => '+20', 'continent' => 'Afrique', 'region_id' => 1],
            ['id' => 10, 'nom' => 'Éthiopie', 'code_iso' => 'ET', 'indicatif' => '+251', 'continent' => 'Afrique', 'region_id' => 4],
            ['id' => 11, 'nom' => 'Guinée équatoriale', 'code_iso' => 'GQ', 'indicatif' => '+240', 'continent' => 'Afrique', 'region_id' => 3],
            ['id' => 12, 'nom' => 'Libye', 'code_iso' => 'LY', 'indicatif' => '+218', 'continent' => 'Afrique', 'region_id' => 1],
            ['id' => 13, 'nom' => 'Mali', 'code_iso' => 'ML', 'indicatif' => '+223', 'continent' => 'Afrique', 'region_id' => 2],
            ['id' => 14, 'nom' => 'Maroc', 'code_iso' => 'MA', 'indicatif' => '+212', 'continent' => 'Afrique', 'region_id' => 1],
            ['id' => 15, 'nom' => 'Nigeria', 'code_iso' => 'NG', 'indicatif' => '+234', 'continent' => 'Afrique', 'region_id' => 2],
            ['id' => 16, 'nom' => 'Sao Tomé-et-Principe', 'code_iso' => 'ST', 'indicatif' => '+239', 'continent' => 'Afrique', 'region_id' => 3],
            ['id' => 17, 'nom' => 'Sénégal', 'code_iso' => 'SN', 'indicatif' => '+221', 'continent' => 'Afrique', 'region_id' => 2],
            ['id' => 18, 'nom' => 'Togo', 'code_iso' => 'TG', 'indicatif' => '+228', 'continent' => 'Afrique', 'region_id' => 2],
            ['id' => 19, 'nom' => 'Tunisie', 'code_iso' => 'TN', 'indicatif' => '+216', 'continent' => 'Afrique', 'region_id' => 1],
            ['id' => 20, 'nom' => 'Brésil', 'code_iso' => 'BR', 'indicatif' => '+55', 'continent' => 'Amérique', 'region_id' => 16],
            ['id' => 21, 'nom' => 'Canada', 'code_iso' => 'CA', 'indicatif' => '+1', 'continent' => 'Amérique', 'region_id' => 14],
            ['id' => 22, 'nom' => 'Cuba', 'code_iso' => 'CU', 'indicatif' => '+53', 'continent' => 'Amérique', 'region_id' => 17],
            ['id' => 23, 'nom' => 'États-Unis', 'code_iso' => 'US', 'indicatif' => '+1', 'continent' => 'Amérique', 'region_id' => 14],
            ['id' => 24, 'nom' => 'Arabie saoudite', 'code_iso' => 'SA', 'indicatif' => '+966', 'continent' => 'Asie', 'region_id' => 10],
            ['id' => 25, 'nom' => 'Chine', 'code_iso' => 'CN', 'indicatif' => '+86', 'continent' => 'Asie', 'region_id' => 9],
            ['id' => 26, 'nom' => 'Corée du Sud', 'code_iso' => 'KR', 'indicatif' => '+82', 'continent' => 'Asie', 'region_id' => 13],
            ['id' => 27, 'nom' => 'Inde', 'code_iso' => 'IN', 'indicatif' => '+91', 'continent' => 'Asie', 'region_id' => 12],
            ['id' => 28, 'nom' => 'Japon', 'code_iso' => 'JP', 'indicatif' => '+81', 'continent' => 'Asie', 'region_id' => 13],
            ['id' => 29, 'nom' => 'Liban', 'code_iso' => 'LB', 'indicatif' => '+961', 'continent' => 'Asie', 'region_id' => 10],
            ['id' => 30, 'nom' => 'Turquie', 'code_iso' => 'TR', 'indicatif' => '+90', 'continent' => 'Asie', 'region_id' => 8],
            ['id' => 31, 'nom' => 'Allemagne', 'code_iso' => 'DE', 'indicatif' => '+49', 'continent' => 'Europe', 'region_id' => 6],
            ['id' => 32, 'nom' => 'Belgique', 'code_iso' => 'BE', 'indicatif' => '+32', 'continent' => 'Europe', 'region_id' => 6],
            ['id' => 33, 'nom' => 'Espagne', 'code_iso' => 'ES', 'indicatif' => '+34', 'continent' => 'Europe', 'region_id' => 8],
            ['id' => 34, 'nom' => 'France', 'code_iso' => 'FR', 'indicatif' => '+33', 'continent' => 'Europe', 'region_id' => 6],
            ['id' => 35, 'nom' => 'Italie', 'code_iso' => 'IT', 'indicatif' => '+39', 'continent' => 'Europe', 'region_id' => 8],
            ['id' => 36, 'nom' => 'Royaume-Uni', 'code_iso' => 'GB', 'indicatif' => '+44', 'continent' => 'Europe', 'region_id' => 6],
            ['id' => 37, 'nom' => 'Russie', 'code_iso' => 'RU', 'indicatif' => '+7', 'continent' => 'Europe', 'region_id' => 7],
            ['id' => 38, 'nom' => 'Vatican', 'code_iso' => 'VA', 'indicatif' => '+379', 'continent' => 'Europe', 'region_id' => 8],
            ['id' => 39, 'nom' => 'République centrafricaine', 'code_iso' => 'CF', 'indicatif' => '+236', 'continent' => 'Afrique', 'region_id' => 3],
            ['id' => 40, 'nom' => 'Gabon', 'code_iso' => 'GA', 'indicatif' => '+241', 'continent' => 'Afrique', 'region_id' => 3],
        ];

        DB::table('pays')->insert($pays);
    }

    private function seedVille()
    {
        $villes = [
            ['id' => 1, 'nom' => 'Pretoria', 'pays_id' => 1],
            ['id' => 2, 'nom' => 'Alger', 'pays_id' => 2],
            ['id' => 3, 'nom' => 'Luanda', 'pays_id' => 3],
            ['id' => 4, 'nom' => 'Cotonou', 'pays_id' => 4],
            ['id' => 5, 'nom' => 'Yaoundé', 'pays_id' => 5],
            ['id' => 6, 'nom' => 'Brazzaville', 'pays_id' => 6],
            ['id' => 7, 'nom' => 'Kinshasa', 'pays_id' => 7],
            ['id' => 8, 'nom' => 'Abidjan', 'pays_id' => 8],
            ['id' => 9, 'nom' => 'Le Caire', 'pays_id' => 9],
            ['id' => 10, 'nom' => 'Addis Ababa', 'pays_id' => 10],
            ['id' => 11, 'nom' => 'Malabo', 'pays_id' => 11],
            ['id' => 12, 'nom' => 'Bata', 'pays_id' => 11],
            ['id' => 13, 'nom' => 'Tripoli', 'pays_id' => 12],
            ['id' => 14, 'nom' => 'Bamako', 'pays_id' => 13],
            ['id' => 15, 'nom' => 'Rabat', 'pays_id' => 14],
            ['id' => 16, 'nom' => 'Abuja', 'pays_id' => 15],
            ['id' => 17, 'nom' => 'São Tomé', 'pays_id' => 16],
            ['id' => 18, 'nom' => 'Dakar', 'pays_id' => 17],
            ['id' => 19, 'nom' => 'Lomé', 'pays_id' => 18],
            ['id' => 20, 'nom' => 'Tunis', 'pays_id' => 19],
            ['id' => 21, 'nom' => 'Brasília', 'pays_id' => 20],
            ['id' => 22, 'nom' => 'Ottawa', 'pays_id' => 21],
            ['id' => 23, 'nom' => 'La Havane', 'pays_id' => 22],
            ['id' => 24, 'nom' => 'Washington', 'pays_id' => 23],
            ['id' => 25, 'nom' => 'Riyad', 'pays_id' => 24],
            ['id' => 26, 'nom' => 'Pékin', 'pays_id' => 25],
            ['id' => 27, 'nom' => 'Séoul', 'pays_id' => 26],
            ['id' => 28, 'nom' => 'New Delhi', 'pays_id' => 27],
            ['id' => 29, 'nom' => 'Tokyo', 'pays_id' => 28],
            ['id' => 30, 'nom' => 'Beyrouth', 'pays_id' => 29],
            ['id' => 31, 'nom' => 'Ankara', 'pays_id' => 30],
            ['id' => 32, 'nom' => 'Berlin', 'pays_id' => 31],
            ['id' => 33, 'nom' => 'Bruxelles', 'pays_id' => 32],
            ['id' => 34, 'nom' => 'Madrid', 'pays_id' => 33],
            ['id' => 35, 'nom' => 'Paris', 'pays_id' => 34],
            ['id' => 36, 'nom' => 'Rome', 'pays_id' => 35],
            ['id' => 37, 'nom' => 'Londres', 'pays_id' => 36],
            ['id' => 38, 'nom' => 'Moscou', 'pays_id' => 37],
            ['id' => 39, 'nom' => 'Rome', 'pays_id' => 38],
            ['id' => 40, 'nom' => 'Bangui', 'pays_id' => 39],
        ];

        DB::table('ville')->insert($villes);
    }

    private function seedStructure()
    {
        DB::table('structure')->insert([
            ['id' => 1, 'nom' => 'Université Omar Bongo'],
            ['id' => 2, 'nom' => 'Total Gabon'],
            ['id' => 3, 'nom' => 'Banque des États de l\'Afrique Centrale'],
            ['id' => 4, 'nom' => 'Port Autonome de Libreville'],
            ['id' => 5, 'nom' => 'Ministère des Finances'],
            ['id' => 6, 'nom' => 'Hôpital d\'Instruction des Armées'],
            ['id' => 7, 'nom' => 'Société Gabonaise de Transport'],
        ]);
    }

    private function seedEtablissement()
    {
        DB::table('etablissement')->insert([
            ['id' => 1, 'nom' => 'Université Omar Bongo', 'type' => 'Université', 'ville_id' => 1],
            ['id' => 2, 'nom' => 'Lycée National Léon Mba', 'type' => 'Lycée', 'ville_id' => 1],
            ['id' => 3, 'nom' => 'Institut National des Sciences', 'type' => 'Institut', 'ville_id' => 2],
            ['id' => 4, 'nom' => 'École Nationale d\'Administration', 'type' => 'École', 'ville_id' => 1],
            ['id' => 5, 'nom' => 'Université des Sciences de la Santé', 'type' => 'Université', 'ville_id' => 1],
        ]);
    }

    private function seedEntite()
    {
        DB::table('entite')->insert([
            ['id' => 1, 'nom' => 'Présidence de la République', 'id_sup' => null],
            ['id' => 2, 'nom' => 'Ministère de la Défense', 'id_sup' => null],
            ['id' => 3, 'nom' => 'Ministère de l\'Intérieur', 'id_sup' => null],
            ['id' => 4, 'nom' => 'Assemblée Nationale', 'id_sup' => null],
            ['id' => 5, 'nom' => 'Sénat', 'id_sup' => null],
            ['id' => 6, 'nom' => 'Ambassade du Gabon en France', 'id_sup' => null],
            ['id' => 7, 'nom' => 'Conseil National de la Communication', 'id_sup' => null],
        ]);
    }

    private function seedDecoration()
    {
        DB::table('decoration')->insert([
            ['deco_id' => 1, 'deco_nom' => 'Ordre National du Mérite', 'deco_type' => 'National', 'deco_niveau' => 'Or', 'deco_grade' => 'Grand Officier', 'deco_date_obtention' => '2010-05-01', 'deco_autorite' => 'Président', 'deco_motif' => 'Service rendu', 'deco_description' => 'Décoration nationale pour service exceptionnel', 'deco_fichierAttestation' => 'attestation1.pdf'],
            ['deco_id' => 2, 'deco_nom' => 'Médaille du Travail', 'deco_type' => 'Professionnel', 'deco_niveau' => 'Argent', 'deco_grade' => 'Officier', 'deco_date_obtention' => '2015-09-15', 'deco_autorite' => 'Ministre du Travail', 'deco_motif' => 'Ancienneté', 'deco_description' => 'Récompense pour 30 ans de service', 'deco_fichierAttestation' => 'attestation2.pdf'],
        ]);
    }

    private function seedPv()
    {
        DB::table('pv')->insert([
            ['numero' => 'PV2025-001', 'date' => '2025-01-15', 'description' => 'Procès-verbal de nomination'],
            ['numero' => 'PV2025-002', 'date' => '2025-03-10', 'description' => 'Procès-verbal de réunion du conseil'],
        ]);
    }

    private function seedDignitaire()
    {
        $dignitaires = [
            ['id' => 1, 'nip' => 'NIP001', 'matricule' => 'MAT001', 'nom' => 'BONGO ', 'prenom' => 'Ali', 'date_naissance' => '1959-02-09', 'nationalite' => null, 'lieu_naissance' => 1, 'genre' => 'Homme', 'etat_civil' => 'Marié', 'telephone' => null, 'adresse' => null, 'photo' => 'image1.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 2, 'nip' => 'NIP002', 'matricule' => 'MAT002', 'nom' => 'ONDO', 'prenom' => 'Rose', 'date_naissance' => '1965-05-12', 'nationalite' => null, 'lieu_naissance' => 2, 'genre' => 'Femme', 'etat_civil' => 'Veuve', 'telephone' => null, 'adresse' => null, 'photo' => 'image2.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 3, 'nip' => 'NIP003', 'matricule' => 'MAT003', 'nom' => 'NDONG', 'prenom' => 'Paul', 'date_naissance' => '1971-09-17', 'nationalite' => null, 'lieu_naissance' => 3, 'genre' => 'Homme', 'etat_civil' => 'Célibataire', 'telephone' => null, 'adresse' => null, 'photo' => 'image3.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 4, 'nip' => 'NIP004', 'matricule' => 'MAT004', 'nom' => 'MOUSSA', 'prenom' => 'Fatou', 'date_naissance' => '1968-11-23', 'nationalite' => null, 'lieu_naissance' => 4, 'genre' => 'Femme', 'etat_civil' => 'Mariée', 'telephone' => null, 'adresse' => null, 'photo' => 'image1.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 5, 'nip' => 'NIP005', 'matricule' => 'MAT005', 'nom' => 'MEYE ', 'prenom' => 'Serge', 'date_naissance' => '1975-03-30', 'nationalite' => null, 'lieu_naissance' => 1, 'genre' => 'Homme', 'etat_civil' => 'divorcé', 'telephone' => null, 'adresse' => null, 'photo' => 'image2.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 6, 'nip' => 'NIP006', 'matricule' => 'MAT006', 'nom' => 'NDONG', 'prenom' => 'Raymond', 'date_naissance' => '1970-05-15', 'nationalite' => 'Gabonaise', 'lieu_naissance' => 1, 'genre' => 'Homme', 'etat_civil' => 'Marié(e)', 'telephone' => '0712345678', 'adresse' => 'Libreville', 'photo' => 'image3.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 7, 'nip' => 'NIP007', 'matricule' => 'MAT007', 'nom' => 'BOUMBA', 'prenom' => 'Clarisse', 'date_naissance' => '1975-08-20', 'nationalite' => 'Gabonaise', 'lieu_naissance' => 2, 'genre' => 'Femme', 'etat_civil' => 'Célibataire', 'telephone' => '0623456789', 'adresse' => 'Port-Gentil', 'photo' => 'image1.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 8, 'nip' => 'NIP008', 'matricule' => 'MAT008', 'nom' => 'MABICKA', 'prenom' => 'Jean-Paul', 'date_naissance' => '1965-03-10', 'nationalite' => 'Gabonaise', 'lieu_naissance' => 3, 'genre' => 'Homme', 'etat_civil' => 'Marié(e)', 'telephone' => '0777777777', 'adresse' => 'Franceville', 'photo' => 'image2.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 9, 'nip' => 'NIP009', 'matricule' => 'MAT009', 'nom' => 'NTOUTOUME', 'prenom' => 'Agnès', 'date_naissance' => '1980-09-12', 'nationalite' => 'Gabonaise', 'lieu_naissance' => 4, 'genre' => 'Femme', 'etat_civil' => 'Marié(e)', 'telephone' => '0611223344', 'adresse' => 'Lambaréné', 'photo' => 'image3.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 10, 'nip' => 'NIP010', 'matricule' => 'MAT0010', 'nom' => 'OKOME', 'prenom' => 'Franck', 'date_naissance' => '1982-11-23', 'nationalite' => 'Gabonaise', 'lieu_naissance' => 5, 'genre' => 'Homme', 'etat_civil' => 'Célibataire', 'telephone' => '0666778899', 'adresse' => 'Oyem', 'photo' => 'image1.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 11, 'nip' => 'NIP0011', 'matricule' => 'MAT0011', 'nom' => 'MBOUMBA', 'prenom' => 'Georgette', 'date_naissance' => '1978-02-28', 'nationalite' => 'Gabonaise', 'lieu_naissance' => 1, 'genre' => 'Femme', 'etat_civil' => 'Veuve', 'telephone' => '0755566666', 'adresse' => 'Mouila', 'photo' => 'image2.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 12, 'nip' => 'NIP0012', 'matricule' => 'MAT0012', 'nom' => 'BONGO', 'prenom' => 'Albert', 'date_naissance' => '1955-07-30', 'nationalite' => 'Gabonaise', 'lieu_naissance' => 2, 'genre' => 'Homme', 'etat_civil' => 'Marié(e)', 'telephone' => '0600001122', 'adresse' => 'Libreville', 'photo' => 'image3.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 13, 'nip' => 'NIP0013', 'matricule' => 'MAT0013', 'nom' => 'MBINA', 'prenom' => 'Rose', 'date_naissance' => '1985-01-05', 'nationalite' => 'Gabonaise', 'lieu_naissance' => 3, 'genre' => 'Femme', 'etat_civil' => 'Célibataire', 'telephone' => '0688996655', 'adresse' => 'Port-Gentil', 'photo' => 'image1.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 14, 'nip' => 'NIP0014', 'matricule' => 'MAT0014', 'nom' => 'KOUMBA', 'prenom' => 'Richard', 'date_naissance' => '1972-06-17', 'nationalite' => 'Gabonaise', 'lieu_naissance' => 4, 'genre' => 'Homme', 'etat_civil' => 'Divorcé(e)', 'telephone' => '0701010101', 'adresse' => 'Franceville', 'photo' => 'image2.png', 'casierJud' => null, 'certificatsMed' => null],
            ['id' => 15, 'nip' => 'NIP015', 'matricule' => 'MAT015', 'nom' => 'NGUEMA', 'prenom' => 'Sylvia', 'date_naissance' => '1990-12-11', 'nationalite' => 'Gabonaise', 'lieu_naissance' => 5, 'genre' => 'Femme', 'etat_civil' => 'Célibataire', 'telephone' => '0696969696', 'adresse' => 'Bitam', 'photo' => 'image1.png', 'casierJud' => null, 'certificatsMed' => null],
        ];

        DB::table('dignitaire')->insert($dignitaires);
    }

    private function seedDiplome()
    {
        DB::table('diplome')->insert([
            ['id' => 1, 'dignitaire_id' => 1, 'intitule' => 'Doctorat en Droit', 'etablissement_id' => 1, 'annee' => '1990', 'ville_id' => 1, 'domaine_id' => 2, 'code' => 'DOC001', 'type' => 'Doctorat'],
            ['id' => 2, 'dignitaire_id' => 2, 'intitule' => 'Master Sciences Politiques', 'etablissement_id' => 2, 'annee' => '1992', 'ville_id' => 2, 'domaine_id' => 1, 'code' => 'MAS001', 'type' => 'Master'],
            ['id' => 3, 'dignitaire_id' => 3, 'intitule' => 'Licence Informatique', 'etablissement_id' => 3, 'annee' => '1995', 'ville_id' => 3, 'domaine_id' => 5, 'code' => 'LIC001', 'type' => 'Licence'],
            ['id' => 4, 'dignitaire_id' => 4, 'intitule' => 'DESS Gestion', 'etablissement_id' => 4, 'annee' => '1993', 'ville_id' => 4, 'domaine_id' => 7, 'code' => 'DESS001', 'type' => 'DESS'],
            ['id' => 5, 'dignitaire_id' => 5, 'intitule' => 'CAPES Lettres Modernes', 'etablissement_id' => 5, 'annee' => '1991', 'ville_id' => 1, 'domaine_id' => 6, 'code' => 'CAP001', 'type' => 'CAPES'],
        ]);
    }

    private function seedEnfants()
    {
        DB::table('enfants')->insert([
            ['id' => 1, 'nom' => 'BONGO', 'prenom' => 'Junior', 'date_naissance' => '1990-07-14', 'lieu_naissance' => 1, 'genre' => 'Homme', 'dignitaire_id' => 1],
            ['id' => 2, 'nom' => 'BONGO', 'prenom' => 'Sylvia', 'date_naissance' => '1994-01-22', 'lieu_naissance' => 2, 'genre' => 'Femme', 'dignitaire_id' => 1],
            ['id' => 3, 'nom' => 'ONDO', 'prenom' => 'Patrick', 'date_naissance' => '1989-05-12', 'lieu_naissance' => 2, 'genre' => 'Homme', 'dignitaire_id' => 2],
            ['id' => 4, 'nom' => 'MOUSSA', 'prenom' => 'Mariama', 'date_naissance' => '2002-08-05', 'lieu_naissance' => 4, 'genre' => 'Femme', 'dignitaire_id' => 3],
            ['id' => 5, 'nom' => 'MEYE', 'prenom' => 'Nicolas', 'date_naissance' => '2003-12-20', 'lieu_naissance' => 1, 'genre' => 'Homme', 'dignitaire_id' => 4],
        ]);
    }

    private function seedLangues()
    {
        DB::table('langues')->insert([
            ['id' => 1, 'dignitaire_id' => 1, 'langue_id' => 1, 'niveau' => 'Courant'],
            ['id' => 2, 'dignitaire_id' => 1, 'langue_id' => 2, 'niveau' => 'Moyen'],
            ['id' => 3, 'dignitaire_id' => 2, 'langue_id' => 1, 'niveau' => 'Courant'],
            ['id' => 4, 'dignitaire_id' => 2, 'langue_id' => 4, 'niveau' => 'Débutant'],
            ['id' => 5, 'dignitaire_id' => 3, 'langue_id' => 5, 'niveau' => 'Bilingue'],
        ]);
    }

    private function seedExperiences()
    {
        DB::table('experiences')->insert([
            ['id' => 1, 'dignitaire_id' => 1, 'intitule' => 'Avocat à la Cour', 'date_debut' => '1985-01-01', 'date_fin' => '1990-12-31', 'structure_id' => 1],
            ['id' => 2, 'dignitaire_id' => 2, 'intitule' => 'Directrice Cabinet Ministériel', 'date_debut' => '2000-04-10', 'date_fin' => '2005-09-15', 'structure_id' => 5],
            ['id' => 3, 'dignitaire_id' => 3, 'intitule' => 'Ingénieur Systèmes', 'date_debut' => '2002-05-05', 'date_fin' => '2010-07-30', 'structure_id' => 2],
            ['id' => 4, 'dignitaire_id' => 4, 'intitule' => 'Chargée de Mission', 'date_debut' => '2007-03-01', 'date_fin' => '2014-02-28', 'structure_id' => 3],
            ['id' => 5, 'dignitaire_id' => 5, 'intitule' => 'Chef de Service', 'date_debut' => '2011-11-01', 'date_fin' => '2016-06-30', 'structure_id' => 4],
        ]);
    }

    private function seedPostes()
    {
        $postes = [
            ['id' => 1, 'dignitaire_id' => 1, 'intitule' => 'Président de la République', 'date_debut' => '2009-10-16', 'date_fin' => '2025-04-12', 'entite_id' => 1, 'ville_id' => 1],
            ['id' => 2, 'dignitaire_id' => 2, 'intitule' => 'Ministre de l\'Intérieur', 'date_debut' => '2010-03-20', 'date_fin' => '2016-08-05', 'entite_id' => 2, 'ville_id' => 2],
            ['id' => 3, 'dignitaire_id' => 3, 'intitule' => 'Député', 'date_debut' => '2014-02-12', 'date_fin' => null, 'entite_id' => 4, 'ville_id' => 3],
            ['id' => 4, 'dignitaire_id' => 4, 'intitule' => 'Ambassadrice', 'date_debut' => '2017-09-08', 'date_fin' => '2022-06-30', 'entite_id' => 6, 'ville_id' => 4],
            ['id' => 5, 'dignitaire_id' => 5, 'intitule' => 'Sénateur', 'date_debut' => '2015-07-01', 'date_fin' => '2021-09-25', 'entite_id' => 5, 'ville_id' => 4],
            ['id' => 6, 'dignitaire_id' => 6, 'intitule' => 'Directeur Général', 'date_debut' => '2016-01-15', 'date_fin' => null, 'entite_id' => 2, 'ville_id' => 2],
            ['id' => 7, 'dignitaire_id' => 7, 'intitule' => 'Secrétaire Général', 'date_debut' => '2018-10-01', 'date_fin' => null, 'entite_id' => 2, 'ville_id' => 5],
            ['id' => 8, 'dignitaire_id' => 8, 'intitule' => 'Chef de Service', 'date_debut' => '2008-03-17', 'date_fin' => '2017-04-20', 'entite_id' => 2, 'ville_id' => 1],
            ['id' => 9, 'dignitaire_id' => 9, 'intitule' => 'Conseiller Spécial', 'date_debut' => '2019-06-11', 'date_fin' => '2023-01-01', 'entite_id' => 1, 'ville_id' => 6],
            ['id' => 10, 'dignitaire_id' => 10, 'intitule' => 'Directeur de Cabinet', 'date_debut' => '2012-10-10', 'date_fin' => null, 'entite_id' => 7, 'ville_id' => 7],
            ['id' => 11, 'dignitaire_id' => 11, 'intitule' => 'Gouverneure', 'date_debut' => '2015-06-14', 'date_fin' => '2022-02-10', 'entite_id' => 7, 'ville_id' => 2],
            ['id' => 12, 'dignitaire_id' => 12, 'intitule' => 'Attachée de Presse', 'date_debut' => '2022-01-20', 'date_fin' => '2011-11-30', 'entite_id' => 6, 'ville_id' => 3],
            ['id' => 13, 'dignitaire_id' => 13, 'intitule' => 'Conseillère Diplomatique', 'date_debut' => '2021-02-18', 'date_fin' => null, 'entite_id' => 1, 'ville_id' => 4],
            ['id' => 14, 'dignitaire_id' => 14, 'intitule' => 'Directeur de Finances', 'date_debut' => '2010-02-22', 'date_fin' => '2017-07-15', 'entite_id' => 1, 'ville_id' => 6],
            ['id' => 15, 'dignitaire_id' => 15, 'intitule' => 'Présidente du Sénat', 'date_debut' => '2020-05-14', 'date_fin' => null, 'entite_id' => 7, 'ville_id' => 7],
        ];

        DB::table('postes')->insert($postes);
    }

    private function seedNominations()
    {
        DB::table('nominations')->insert([
            ['id' => 1, 'dignitaire_id' => 1, 'entite_id' => 1, 'poste_id' => null, 'pv_id' => null, 'date_debut' => '2000-01-01', 'date_fin' => '2005-12-31', 'fonction' => 'Président de la République'],
            ['id' => 2, 'dignitaire_id' => 2, 'entite_id' => 2, 'poste_id' => null, 'pv_id' => null, 'date_debut' => '2005-01-01', 'date_fin' => '2010-12-31', 'fonction' => 'Ministre de la Défense'],
            ['id' => 3, 'dignitaire_id' => 3, 'entite_id' => 3, 'poste_id' => null, 'pv_id' => null, 'date_debut' => '2010-01-01', 'date_fin' => '2015-12-31', 'fonction' => 'Ministre de l\'Intérieur'],
            ['id' => 4, 'dignitaire_id' => 4, 'entite_id' => 4, 'poste_id' => null, 'pv_id' => null, 'date_debut' => '2015-01-01', 'date_fin' => '2020-12-31', 'fonction' => 'Présidente de l\'Assemblée Nationale'],
            ['id' => 5, 'dignitaire_id' => 5, 'entite_id' => 5, 'poste_id' => null, 'pv_id' => null, 'date_debut' => '2020-01-01', 'date_fin' => null, 'fonction' => 'Sénateur'],
        ]);
    }

    private function seedHistoriqueNominations()
    {
        DB::table('historique_nominations')->insert([
            ['nomination_id' => 1, 'dignitaire_id' => 1, 'poste_id' => 1, 'entite_id' => 1, 'date_nomination' => '2000-01-01', 'date_fin' => '2005-12-31', 'description' => 'Premier mandat'],
            ['nomination_id' => 2, 'dignitaire_id' => 2, 'poste_id' => 2, 'entite_id' => 2, 'date_nomination' => '2005-01-01', 'date_fin' => '2010-12-31', 'description' => 'Ministre de la Défense'],
        ]);
    }

    private function seedDecorationDignitaire()
    {
        DB::table('decoration_dignitaire')->insert([
            ['dignitaire_id' => 1, 'decoration_id' => 1, 'date_attribution' => '2010-05-01'],
            ['dignitaire_id' => 2, 'decoration_id' => 2, 'date_attribution' => '2015-09-15'],
        ]);
    }
}
