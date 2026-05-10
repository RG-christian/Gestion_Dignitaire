<?php
/**
 * Test simple de l'endpoint dignitaires
 */

$apiBase = 'http://localhost:8000/api';

// 1. Se connecter
$ch = curl_init("$apiBase/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'username' => 'admin',
    'password' => 'admin123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$loginData = json_decode($response, true);
curl_close($ch);

if (!isset($loginData['token'])) {
    echo "Erreur de connexion\n";
    print_r($loginData);
    exit(1);
}

$token = $loginData['token'];
echo "✓ Connecté avec token: " . substr($token, 0, 20) . "...\n\n";

// 2. Récupérer les dignitaires
$ch = curl_init("$apiBase/dignitaires?per_page=5");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Code HTTP: $httpCode\n";
echo "Réponse:\n";
echo $response . "\n";

$data = json_decode($response, true);

if ($httpCode === 200 && isset($data['data'])) {
    echo "\n✓ " . count($data['data']) . " dignitaires récupérés\n";
    echo "Total dans la base: " . $data['total'] . "\n\n";
    
    if (count($data['data']) > 0) {
        echo "Premier dignitaire:\n";
        $first = $data['data'][0];
        echo "  - ID: " . $first['id'] . "\n";
        echo "  - Nom: " . ($first['nom'] ?? 'N/A') . "\n";
        echo "  - Prénom: " . ($first['prenom'] ?? 'N/A') . "\n";
        echo "  - Matricule: " . ($first['matricule'] ?? 'N/A') . "\n";
    }
} else {
    echo "\n✗ Erreur lors de la récupération\n";
}
