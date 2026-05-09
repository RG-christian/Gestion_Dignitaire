#!/usr/bin/env php
<?php
/**
 * Script pour ajouter les use statements dans tous les contrôleurs
 */

$controllers = glob('controllers/*.php');

$namespaceMap = [
    'UserDAO' => 'classes\\UserDAO',
    'DignitaireDAO' => 'classes\\DignitaireDAO',
    'DiplomeDAO' => 'classes\\DiplomeDAO',
    'EnfantDAO' => 'classes\\EnfantDAO',
    'DecorationDAO' => 'classes\\DecorationDAO',
    'NominationDAO' => 'classes\\NominationDAO',
    'PosteDAO' => 'classes\\PosteDAO',
    'ExperienceDAO' => 'classes\\ExperienceDAO',
    'LangueParleeDAO' => 'classes\\LangueParleeDAO',
    'PaysDAO' => 'classes\\PaysDAO',
    'RegionDAO' => 'classes\\RegionDAO',
    'VilleDAO' => 'classes\\VilleDAO',
    'StructureDAO' => 'classes\\StructureDAO',
    'Dignitaire' => 'classes\\Dignitaire',
    'Diplome' => 'classes\\Diplome',
    'Enfant' => 'classes\\Enfant',
    'Decoration' => 'classes\\Decoration',
    'Nomination' => 'classes\\Nomination',
    'Poste' => 'classes\\Poste',
    'Experience' => 'classes\\Experience',
    'LangueParlee' => 'classes\\LangueParlee',
    'Pays' => 'classes\\Pays',
    'Region' => 'classes\\Region',
    'Ville' => 'classes\\Ville',
    'Structure' => 'classes\\Structure',
];

foreach ($controllers as $file) {
    echo "Traitement de $file...\n";
    
    $content = file_get_contents($file);
    $usedClasses = [];
    
    // Détecter quelles classes sont utilisées
    foreach ($namespaceMap as $className => $namespace) {
        if (preg_match('/new\s+' . preg_quote($className) . '\s*\(/', $content)) {
            $usedClasses[$className] = $namespace;
        }
    }
    
    if (empty($usedClasses)) {
        echo "  Aucune classe à importer\n";
        continue;
    }
    
    // Vérifier si les use statements existent déjà
    $newUseStatements = [];
    foreach ($usedClasses as $className => $namespace) {
        if (!preg_match('/use\s+' . preg_quote($namespace) . '\s*;/', $content)) {
            $newUseStatements[] = "use $namespace;";
        }
    }
    
    if (empty($newUseStatements)) {
        echo "  Tous les use statements sont déjà présents\n";
        continue;
    }
    
    // Ajouter les use statements après le <?php
    $useBlock = "\n" . implode("\n", $newUseStatements) . "\n";
    
    // Trouver la position après <?php et les use existants
    if (preg_match('/^<\?php\s*\n((?:use\s+[^;]+;\s*\n)*)/s', $content, $matches)) {
        $content = preg_replace(
            '/^(<\?php\s*\n(?:use\s+[^;]+;\s*\n)*)/s',
            '$1' . $useBlock,
            $content,
            1
        );
    } else {
        $content = preg_replace('/^<\?php\s*\n/', "<?php\n$useBlock", $content, 1);
    }
    
    file_put_contents($file, $content);
    echo "  ✅ Ajouté: " . implode(', ', array_keys($usedClasses)) . "\n";
}

echo "\n✅ Terminé!\n";
