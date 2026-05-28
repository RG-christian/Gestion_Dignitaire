<?php

/**
 * Script pour ajouter les provinces et villes principales des 40 pays
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🌍 Ajout des provinces et villes principales...\n\n";

// Données : pays => [provinces => [villes]]
$data = [
    'Gabon' => [
        'Estuaire' => ['Libreville', 'Ntoum', 'Kango', 'Cocobeach'],
        'Haut-Ogooué' => ['Franceville', 'Moanda', 'Akiéni', 'Okondja'],
        'Moyen-Ogooué' => ['Lambaréné', 'Ndjolé', 'Bifoun'],
        'Ngounié' => ['Mouila', 'Ndendé', 'Mimongo', 'Mbigou'],
        'Nyanga' => ['Tchibanga', 'Mayumba', 'Moulengui-Binza'],
        'Ogooué-Ivindo' => ['Makokou', 'Mékambo', 'Ovan', 'Booué'],
        'Ogooué-Lolo' => ['Koulamoutou', 'Lastoursville', 'Pana'],
        'Ogooué-Maritime' => ['Port-Gentil', 'Omboué', 'Gamba'],
        'Woleu-Ntem' => ['Oyem', 'Bitam', 'Mitzic', 'Minvoul']
    ],
    'France' => [
        'Île-de-France' => ['Paris', 'Versailles', 'Boulogne-Billancourt', 'Saint-Denis'],
        'Provence-Alpes-Côte d\'Azur' => ['Marseille', 'Nice', 'Toulon', 'Aix-en-Provence'],
        'Auvergne-Rhône-Alpes' => ['Lyon', 'Grenoble', 'Saint-Étienne', 'Clermont-Ferrand'],
        'Nouvelle-Aquitaine' => ['Bordeaux', 'Limoges', 'Poitiers', 'La Rochelle'],
        'Occitanie' => ['Toulouse', 'Montpellier', 'Nîmes', 'Perpignan']
    ],
    'Cameroun' => [
        'Centre' => ['Yaoundé', 'Mbalmayo', 'Obala', 'Akonolinga'],
        'Littoral' => ['Douala', 'Edéa', 'Nkongsamba'],
        'Ouest' => ['Bafoussam', 'Dschang', 'Mbouda'],
        'Nord-Ouest' => ['Bamenda', 'Kumbo', 'Wum'],
        'Sud-Ouest' => ['Buéa', 'Limbé', 'Kumba']
    ],
    'Sénégal' => [
        'Dakar' => ['Dakar', 'Pikine', 'Guédiawaye', 'Rufisque'],
        'Thiès' => ['Thiès', 'Mbour', 'Tivaouane'],
        'Saint-Louis' => ['Saint-Louis', 'Dagana', 'Podor'],
        'Diourbel' => ['Diourbel', 'Touba', 'Mbacké'],
        'Kaolack' => ['Kaolack', 'Nioro du Rip', 'Guinguinéo']
    ],
    'Côte dIvoire' => [
        'Abidjan' => ['Abidjan', 'Bingerville', 'Anyama'],
        'Yamoussoukro' => ['Yamoussoukro', 'Toumodi', 'Didiévi'],
        'Bouaké' => ['Bouaké', 'Katiola', 'Sakassou'],
        'San-Pédro' => ['San-Pédro', 'Tabou', 'Sassandra'],
        'Korhogo' => ['Korhogo', 'Ferkessédougou', 'Boundiali']
    ],
    'Congo Brazaville' => [
        'Brazzaville' => ['Brazzaville', 'Kintélé', 'Ignié'],
        'Pointe-Noire' => ['Pointe-Noire', 'Loango', 'Tchiamba-Nzassi'],
        'Plateaux' => ['Djambala', 'Gamboma', 'Lékana'],
        'Cuvette' => ['Owando', 'Makoua', 'Boundji'],
        'Sangha' => ['Ouesso', 'Sembé', 'Souanké']
    ],
    'RD Congo' => [
        'Kinshasa' => ['Kinshasa', 'Ndjili', 'Masina'],
        'Kongo-Central' => ['Matadi', 'Boma', 'Mbanza-Ngungu'],
        'Katanga' => ['Lubumbashi', 'Likasi', 'Kolwezi'],
        'Kasaï' => ['Kananga', 'Tshikapa', 'Mbuji-Mayi'],
        'Nord-Kivu' => ['Goma', 'Butembo', 'Beni']
    ],
    'Bénin' => [
        'Littoral' => ['Cotonou'],
        'Atlantique' => ['Abomey-Calavi', 'Ouidah', 'Allada'],
        'Ouémé' => ['Porto-Novo', 'Adjarra', 'Sèmè-Kpodji'],
        'Borgou' => ['Parakou', 'Nikki', 'Tchaourou'],
        'Zou' => ['Abomey', 'Bohicon', 'Djidja']
    ],
    'Togo' => [
        'Maritime' => ['Lomé', 'Aného', 'Tsévié'],
        'Plateaux' => ['Atakpamé', 'Kpalimé', 'Notsé'],
        'Centrale' => ['Sokodé', 'Tchamba', 'Sotouboua'],
        'Kara' => ['Kara', 'Bassar', 'Niamtougou'],
        'Savanes' => ['Dapaong', 'Mango', 'Cinkassé']
    ],
    'Mali' => [
        'Bamako' => ['Bamako'],
        'Kayes' => ['Kayes', 'Kita', 'Nioro du Sahel'],
        'Koulikoro' => ['Koulikoro', 'Kati', 'Kolokani'],
        'Sikasso' => ['Sikasso', 'Koutiala', 'Kadiolo'],
        'Ségou' => ['Ségou', 'San', 'Bla']
    ],
    'Nigeria' => [
        'Lagos' => ['Lagos', 'Ikeja', 'Epe'],
        'Abuja' => ['Abuja', 'Gwagwalada', 'Kuje'],
        'Kano' => ['Kano', 'Wudil', 'Bichi'],
        'Rivers' => ['Port Harcourt', 'Bonny', 'Okrika'],
        'Oyo' => ['Ibadan', 'Oyo', 'Ogbomosho']
    ],
    'Maroc' => [
        'Casablanca-Settat' => ['Casablanca', 'Mohammedia', 'El Jadida'],
        'Rabat-Salé-Kénitra' => ['Rabat', 'Salé', 'Kénitra'],
        'Fès-Meknès' => ['Fès', 'Meknès', 'Taza'],
        'Marrakech-Safi' => ['Marrakech', 'Safi', 'Essaouira'],
        'Tanger-Tétouan-Al Hoceïma' => ['Tanger', 'Tétouan', 'Al Hoceïma']
    ],
    'Algérie' => [
        'Alger' => ['Alger', 'Bab El Oued', 'Hussein Dey'],
        'Oran' => ['Oran', 'Aïn El Turk', 'Mers El Kébir'],
        'Constantine' => ['Constantine', 'El Khroub', 'Aïn Smara'],
        'Annaba' => ['Annaba', 'El Hadjar', 'Berrahal'],
        'Blida' => ['Blida', 'Boufarik', 'Larbaa']
    ],
    'Tunisie' => [
        'Tunis' => ['Tunis', 'La Marsa', 'Carthage'],
        'Sfax' => ['Sfax', 'Sakiet Ezzit', 'Sakiet Eddaïer'],
        'Sousse' => ['Sousse', 'Hammam Sousse', 'Msaken'],
        'Kairouan' => ['Kairouan', 'Haffouz', 'Sbikha'],
        'Bizerte' => ['Bizerte', 'Menzel Bourguiba', 'Mateur']
    ],
    'Égypte' => [
        'Le Caire' => ['Le Caire', 'Gizeh', 'Héliopolis'],
        'Alexandrie' => ['Alexandrie', 'Borg El Arab', 'Abou Qir'],
        'Gizeh' => ['Gizeh', '6 Octobre', 'Cheikh Zayed'],
        'Charm el-Cheikh' => ['Charm el-Cheikh', 'Dahab', 'Nuweiba'],
        'Louxor' => ['Louxor', 'Karnak', 'Esna']
    ],
    'Afrique du Sud' => [
        'Gauteng' => ['Johannesburg', 'Pretoria', 'Soweto'],
        'Western Cape' => ['Le Cap', 'Stellenbosch', 'Paarl'],
        'KwaZulu-Natal' => ['Durban', 'Pietermaritzburg', 'Richards Bay'],
        'Eastern Cape' => ['Port Elizabeth', 'East London', 'Mthatha'],
        'Mpumalanga' => ['Nelspruit', 'Witbank', 'Middelburg']
    ],
    'Éthiopie' => [
        'Addis-Abeba' => ['Addis-Abeba', 'Bole', 'Kirkos'],
        'Oromia' => ['Adama', 'Jimma', 'Bishoftu'],
        'Amhara' => ['Bahir Dar', 'Gondar', 'Dessie'],
        'Tigré' => ['Mekele', 'Adigrat', 'Axoum'],
        'Somali' => ['Jijiga', 'Gode', 'Kebri Dehar']
    ],
    'Angola' => [
        'Luanda' => ['Luanda', 'Viana', 'Cacuaco'],
        'Huambo' => ['Huambo', 'Caála', 'Longonjo'],
        'Benguela' => ['Benguela', 'Lobito', 'Catumbela'],
        'Cabinda' => ['Cabinda', 'Cacongo', 'Buco-Zau'],
        'Huíla' => ['Lubango', 'Chibia', 'Matala']
    ],
    'Guinée équatoriale' => [
        'Bioko Norte' => ['Malabo', 'Rebola', 'Baney'],
        'Litoral' => ['Bata', 'Mbini', 'Cogo'],
        'Centro Sur' => ['Evinayong', 'Acurenam', 'Bicurga'],
        'Wele-Nzas' => ['Mongomo', 'Nsork', 'Aconibe']
    ],
    'Sao Tomé-et-Principe' => [
        'São Tomé' => ['São Tomé', 'Trindade', 'Guadalupe'],
        'Príncipe' => ['Santo António', 'Porto Real']
    ],
    'Libye' => [
        'Tripoli' => ['Tripoli', 'Tajoura', 'Janzour'],
        'Benghazi' => ['Benghazi', 'Al Marj', 'Ajdabiya'],
        'Misrata' => ['Misrata', 'Zliten', 'Tawergha'],
        'Sabha' => ['Sabha', 'Ubari', 'Murzuq']
    ],
    'République centrafricaine' => [
        'Bangui' => ['Bangui'],
        'Ombella-M\'Poko' => ['Bimbo', 'Damara', 'Boali'],
        'Ouham' => ['Bossangoa', 'Bouca', 'Batangafo'],
        'Lobaye' => ['Mbaïki', 'Mongoumba', 'Boganda']
    ],
    'États-Unis' => [
        'Californie' => ['Los Angeles', 'San Francisco', 'San Diego', 'Sacramento'],
        'New York' => ['New York', 'Buffalo', 'Rochester', 'Albany'],
        'Texas' => ['Houston', 'Dallas', 'Austin', 'San Antonio'],
        'Floride' => ['Miami', 'Orlando', 'Tampa', 'Jacksonville'],
        'Illinois' => ['Chicago', 'Aurora', 'Naperville', 'Rockford']
    ],
    'Canada' => [
        'Ontario' => ['Toronto', 'Ottawa', 'Mississauga', 'Hamilton'],
        'Québec' => ['Montréal', 'Québec', 'Laval', 'Gatineau'],
        'Colombie-Britannique' => ['Vancouver', 'Victoria', 'Surrey', 'Burnaby'],
        'Alberta' => ['Calgary', 'Edmonton', 'Red Deer', 'Lethbridge'],
        'Manitoba' => ['Winnipeg', 'Brandon', 'Steinbach']
    ],
    'Brésil' => [
        'São Paulo' => ['São Paulo', 'Campinas', 'Santos', 'Guarulhos'],
        'Rio de Janeiro' => ['Rio de Janeiro', 'Niterói', 'Duque de Caxias'],
        'Minas Gerais' => ['Belo Horizonte', 'Uberlândia', 'Contagem'],
        'Bahia' => ['Salvador', 'Feira de Santana', 'Vitória da Conquista'],
        'Paraná' => ['Curitiba', 'Londrina', 'Maringá']
    ],
    'Cuba' => [
        'La Havane' => ['La Havane', 'Marianao', 'Guanabacoa'],
        'Santiago de Cuba' => ['Santiago de Cuba', 'Palma Soriano'],
        'Holguín' => ['Holguín', 'Moa', 'Banes'],
        'Camagüey' => ['Camagüey', 'Florida', 'Nuevitas']
    ],
    'Chine' => [
        'Pékin' => ['Pékin', 'Chaoyang', 'Haidian'],
        'Shanghai' => ['Shanghai', 'Pudong', 'Baoshan'],
        'Guangdong' => ['Guangzhou', 'Shenzhen', 'Dongguan'],
        'Zhejiang' => ['Hangzhou', 'Ningbo', 'Wenzhou'],
        'Jiangsu' => ['Nanjing', 'Suzhou', 'Wuxi']
    ],
    'Japon' => [
        'Tokyo' => ['Tokyo', 'Shibuya', 'Shinjuku'],
        'Osaka' => ['Osaka', 'Sakai', 'Higashiosaka'],
        'Kyoto' => ['Kyoto', 'Uji', 'Kameoka'],
        'Hokkaido' => ['Sapporo', 'Asahikawa', 'Hakodate'],
        'Fukuoka' => ['Fukuoka', 'Kitakyushu', 'Kurume']
    ],
    'Inde' => [
        'Maharashtra' => ['Mumbai', 'Pune', 'Nagpur'],
        'Delhi' => ['New Delhi', 'Delhi', 'Gurgaon'],
        'Karnataka' => ['Bangalore', 'Mysore', 'Mangalore'],
        'Tamil Nadu' => ['Chennai', 'Coimbatore', 'Madurai'],
        'Gujarat' => ['Ahmedabad', 'Surat', 'Vadodara']
    ],
    'Corée du Sud' => [
        'Séoul' => ['Séoul', 'Gangnam', 'Jongno'],
        'Busan' => ['Busan', 'Haeundae', 'Suyeong'],
        'Incheon' => ['Incheon', 'Namdong', 'Bupyeong'],
        'Daegu' => ['Daegu', 'Suseong', 'Dalseo'],
        'Daejeon' => ['Daejeon', 'Yuseong', 'Seo']
    ],
    'Arabie saoudite' => [
        'Riyad' => ['Riyad', 'Diriyah', 'Al Kharj'],
        'La Mecque' => ['La Mecque', 'Djeddah', 'Taëf'],
        'Médine' => ['Médine', 'Yanbu', 'Al Ula'],
        'Province orientale' => ['Dammam', 'Khobar', 'Dhahran']
    ],
    'Turquie' => [
        'Istanbul' => ['Istanbul', 'Kadıköy', 'Beşiktaş'],
        'Ankara' => ['Ankara', 'Çankaya', 'Keçiören'],
        'Izmir' => ['Izmir', 'Konak', 'Karşıyaka'],
        'Antalya' => ['Antalya', 'Alanya', 'Manavgat'],
        'Bursa' => ['Bursa', 'Osmangazi', 'Nilüfer']
    ],
    'Liban' => [
        'Beyrouth' => ['Beyrouth', 'Achrafieh', 'Hamra'],
        'Mont-Liban' => ['Jounieh', 'Byblos', 'Baabda'],
        'Nord' => ['Tripoli', 'Zgharta', 'Batroun'],
        'Sud' => ['Sidon', 'Tyr', 'Nabatieh']
    ],
    'Allemagne' => [
        'Bavière' => ['Munich', 'Nuremberg', 'Augsbourg'],
        'Rhénanie-du-Nord-Westphalie' => ['Cologne', 'Düsseldorf', 'Dortmund'],
        'Bade-Wurtemberg' => ['Stuttgart', 'Mannheim', 'Karlsruhe'],
        'Berlin' => ['Berlin', 'Charlottenburg', 'Kreuzberg'],
        'Hambourg' => ['Hambourg', 'Altona', 'Eimsbüttel']
    ],
    'Belgique' => [
        'Bruxelles-Capitale' => ['Bruxelles', 'Ixelles', 'Schaerbeek'],
        'Flandre-Occidentale' => ['Bruges', 'Ostende', 'Courtrai'],
        'Anvers' => ['Anvers', 'Malines', 'Turnhout'],
        'Liège' => ['Liège', 'Verviers', 'Seraing'],
        'Hainaut' => ['Charleroi', 'Mons', 'La Louvière']
    ],
    'Espagne' => [
        'Madrid' => ['Madrid', 'Móstoles', 'Alcalá de Henares'],
        'Catalogne' => ['Barcelone', 'Hospitalet', 'Badalona'],
        'Andalousie' => ['Séville', 'Málaga', 'Cordoue'],
        'Valence' => ['Valence', 'Alicante', 'Elche'],
        'Pays basque' => ['Bilbao', 'Vitoria-Gasteiz', 'Saint-Sébastien']
    ],
    'Italie' => [
        'Latium' => ['Rome', 'Latina', 'Frosinone'],
        'Lombardie' => ['Milan', 'Bergame', 'Brescia'],
        'Campanie' => ['Naples', 'Salerne', 'Caserte'],
        'Sicile' => ['Palerme', 'Catane', 'Messine'],
        'Vénétie' => ['Venise', 'Vérone', 'Padoue']
    ],
    'Royaume-Uni' => [
        'Angleterre' => ['Londres', 'Manchester', 'Birmingham', 'Liverpool'],
        'Écosse' => ['Édimbourg', 'Glasgow', 'Aberdeen'],
        'Pays de Galles' => ['Cardiff', 'Swansea', 'Newport'],
        'Irlande du Nord' => ['Belfast', 'Derry', 'Lisburn']
    ],
    'Russie' => [
        'Moscou' => ['Moscou', 'Khimki', 'Podolsk'],
        'Saint-Pétersbourg' => ['Saint-Pétersbourg', 'Kolpino', 'Pouchkine'],
        'Tatarstan' => ['Kazan', 'Naberejnye Tchelny', 'Nijni Novgorod'],
        'Krasnodar' => ['Krasnodar', 'Sotchi', 'Novorossiysk'],
        'Sverdlovsk' => ['Iekaterinbourg', 'Nijni Taguil', 'Kamensk-Ouralski']
    ],
    'Vatican' => [
        'Vatican' => ['Cité du Vatican']
    ]
];

$statsProvinces = ['added' => 0, 'updated' => 0, 'skipped' => 0];
$statsVilles = ['added' => 0, 'skipped' => 0];

foreach ($data as $paysNom => $provinces) {
    echo "\n🌍 {$paysNom}\n";
    echo str_repeat('-', 50) . "\n";

    // Vérifier que le pays existe
    $pays = DB::table('pays')->where('nom', $paysNom)->first();
    if (!$pays) {
        echo "  ⚠️  Pays non trouvé, ignoré\n";
        continue;
    }

    foreach ($provinces as $provinceNom => $villes) {
        // Ajouter ou mettre à jour la province
        $province = DB::table('region')->where('nom', $provinceNom)->first();

        if ($province) {
            if ($province->type === 'province' && $province->pays_nom === $paysNom) {
                echo "  ⏭️  Province: {$provinceNom} (existante)\n";
                $statsProvinces['skipped']++;
                $provinceId = $province->id;
            } else {
                DB::table('region')->where('id', $province->id)->update([
                    'type' => 'province',
                    'pays_nom' => $paysNom,
                    'continent' => null
                ]);
                echo "  🔄 Province: {$provinceNom} (mise à jour)\n";
                $statsProvinces['updated']++;
                $provinceId = $province->id;
            }
        } else {
            $provinceId = DB::table('region')->insertGetId([
                'nom' => $provinceNom,
                'type' => 'province',
                'pays_nom' => $paysNom,
                'continent' => null
            ]);
            echo "  ✅ Province: {$provinceNom} (ajoutée)\n";
            $statsProvinces['added']++;
        }

        // Ajouter les villes
        foreach ($villes as $villeNom) {
            $villeExists = DB::table('ville')
                ->where('nom', $villeNom)
                ->where('pays_id', $pays->id)
                ->exists();

            if ($villeExists) {
                $statsVilles['skipped']++;
            } else {
                DB::table('ville')->insert([
                    'nom' => $villeNom,
                    'pays_id' => $pays->id,
                    'region_id' => $provinceId
                ]);
                echo "    • {$villeNom}\n";
                $statsVilles['added']++;
            }
        }
    }
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "📊 RÉSUMÉ FINAL\n";
echo str_repeat('=', 50) . "\n";
echo "Provinces :\n";
echo "  - Ajoutées : {$statsProvinces['added']}\n";
echo "  - Mises à jour : {$statsProvinces['updated']}\n";
echo "  - Existantes : {$statsProvinces['skipped']}\n";
echo "\nVilles :\n";
echo "  - Ajoutées : {$statsVilles['added']}\n";
echo "  - Existantes : {$statsVilles['skipped']}\n";
echo "\n✅ Terminé !\n";
