<?php
/**
 * Script de test direct de l'API Laravel
 * Teste les endpoints sans passer par le frontend
 */

// Configuration
$apiBase = 'http://localhost:8000/api';
$username = 'admin'; // Utilisateur de test
$password = 'admin123'; // Mot de passe de test

echo "=== TEST API GESTION DIGNITAIRES ===\n\n";

// Fonction pour faire des requêtes
function apiRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init($url);
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

// 1. Test de connexion
echo "1. Test de connexion (POST /api/login)\n";
$loginResponse = apiRequest("$apiBase/login", 'POST', [
    'username' => $username,
    'password' => $password
]);

if ($loginResponse['code'] === 200 && isset($loginResponse['body']['token'])) {
    $token = $loginResponse['body']['token'];
    echo "   ✓ Connexion réussie\n";
    echo "   Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "   ✗ Échec de connexion\n";
    echo "   Code HTTP: {$loginResponse['code']}\n";
    echo "   Réponse: " . print_r($loginResponse['body'], true) . "\n";
    exit(1);
}

// 2. Test récupération utilisateur
echo "2. Test récupération utilisateur (GET /api/user)\n";
$userResponse = apiRequest("$apiBase/user", 'GET', null, $token);

if ($userResponse['code'] === 200) {
    echo "   ✓ Utilisateur récupéré\n";
    echo "   Nom: " . ($userResponse['body']['nom_complet'] ?? 'N/A') . "\n\n";
} else {
    echo "   ✗ Échec récupération utilisateur\n";
    echo "   Code HTTP: {$userResponse['code']}\n";
    echo "   Réponse: " . print_r($userResponse['body'], true) . "\n\n";
}

// 3. Test statistiques dashboard
echo "3. Test statistiques dashboard (GET /api/dashboard/stats)\n";
$statsResponse = apiRequest("$apiBase/dashboard/stats", 'GET', null, $token);

if ($statsResponse['code'] === 200) {
    echo "   ✓ Statistiques récupérées\n";
    $stats = $statsResponse['body'];
    echo "   - Dignitaires: " . ($stats['totalDignitaires'] ?? 0) . "\n";
    echo "   - Postes: " . ($stats['totalPostes'] ?? 0) . "\n";
    echo "   - Décorations: " . ($stats['totalDecorations'] ?? 0) . "\n";
    echo "   - Villes: " . ($stats['totalVilles'] ?? 0) . "\n";
    echo "   - Pays: " . ($stats['totalPays'] ?? 0) . "\n";
    echo "   - Régions: " . ($stats['totalRegions'] ?? 0) . "\n\n";
} else {
    echo "   ✗ Échec récupération statistiques\n";
    echo "   Code HTTP: {$statsResponse['code']}\n";
    echo "   Réponse: " . print_r($statsResponse['body'], true) . "\n\n";
}

// 4. Test liste des dignitaires
echo "4. Test liste des dignitaires (GET /api/dignitaires)\n";
$dignitairesResponse = apiRequest("$apiBase/dignitaires?per_page=5", 'GET', null, $token);

if ($dignitairesResponse['code'] === 200) {
    echo "   ✓ Liste des dignitaires récupérée\n";
    $data = $dignitairesResponse['body'];
    echo "   - Total: " . ($data['total'] ?? 0) . "\n";
    echo "   - Page actuelle: " . ($data['current_page'] ?? 0) . "\n";
    echo "   - Par page: " . ($data['per_page'] ?? 0) . "\n";
    
    if (isset($data['data']) && count($data['data']) > 0) {
        echo "   - Premier dignitaire: " . ($data['data'][0]['prenom'] ?? '') . " " . ($data['data'][0]['nom'] ?? '') . "\n";
    }
    echo "\n";
} else {
    echo "   ✗ Échec récupération dignitaires\n";
    echo "   Code HTTP: {$dignitairesResponse['code']}\n";
    echo "   Réponse: " . print_r($dignitairesResponse['body'], true) . "\n\n";
}

// 5. Test référentiels
echo "5. Test référentiels\n";

$referentiels = ['pays', 'regions', 'villes', 'entites'];
foreach ($referentiels as $ref) {
    $refResponse = apiRequest("$apiBase/$ref", 'GET', null, $token);
    if ($refResponse['code'] === 200) {
        $count = is_array($refResponse['body']) ? count($refResponse['body']) : 0;
        echo "   ✓ $ref: $count éléments\n";
    } else {
        echo "   ✗ $ref: Échec (Code {$refResponse['code']})\n";
    }
}

echo "\n=== FIN DES TESTS ===\n";
