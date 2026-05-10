<?php
/**
 * Script de test pour vérifier que l'API Laravel fonctionne
 * Exécuter : php test-api.php
 */

echo "=== TEST API LARAVEL ===\n\n";

// Test 1: Vérifier que le serveur répond
echo "1. Test connexion au serveur...\n";
$ch = curl_init('http://127.0.0.1:8000/api/dashboard/stats');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 401) {
    echo "✅ Serveur répond (401 = authentification requise, c'est normal)\n\n";
} elseif ($httpCode === 200) {
    echo "⚠️  Serveur répond mais sans authentification (pas normal)\n\n";
} else {
    echo "❌ Serveur ne répond pas (code: $httpCode)\n";
    echo "   Vérifiez que 'php artisan serve' est lancé\n\n";
    exit(1);
}

// Test 2: Login
echo "2. Test login...\n";
$ch = curl_init('http://127.0.0.1:8000/api/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'username' => 'admin',
    'password' => 'password'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['token'])) {
        $token = $data['token'];
        echo "✅ Login réussi\n";
        echo "   Token: " . substr($token, 0, 20) . "...\n\n";
    } else {
        echo "❌ Login réussi mais pas de token\n";
        echo "   Réponse: $response\n\n";
        exit(1);
    }
} else {
    echo "❌ Login échoué (code: $httpCode)\n";
    echo "   Réponse: $response\n";
    echo "   Vérifiez les identifiants dans la base de données\n\n";
    exit(1);
}

// Test 3: Stats dashboard
echo "3. Test /api/dashboard/stats...\n";
$ch = curl_init('http://127.0.0.1:8000/api/dashboard/stats');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Stats récupérées\n";
    echo "   Dignitaires: " . ($data['totalDignitaires'] ?? 0) . "\n";
    echo "   Postes: " . ($data['totalPostes'] ?? 0) . "\n";
    echo "   Décorations: " . ($data['totalDecorations'] ?? 0) . "\n";
    echo "   Villes: " . ($data['totalVilles'] ?? 0) . "\n\n";
} else {
    echo "❌ Erreur stats (code: $httpCode)\n";
    echo "   Réponse: $response\n\n";
}

// Test 4: Liste dignitaires
echo "4. Test /api/dignitaires...\n";
$ch = curl_init('http://127.0.0.1:8000/api/dignitaires?per_page=5');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Dignitaires récupérés\n";
    echo "   Total: " . ($data['total'] ?? 0) . "\n";
    echo "   Par page: " . count($data['data'] ?? []) . "\n";
    if (!empty($data['data'])) {
        echo "   Premier: " . ($data['data'][0]['prenom'] ?? '') . " " . ($data['data'][0]['nom'] ?? '') . "\n";
    }
    echo "\n";
} else {
    echo "❌ Erreur dignitaires (code: $httpCode)\n";
    echo "   Réponse: $response\n\n";
}

// Test 5: Villes
echo "5. Test /api/villes...\n";
$ch = curl_init('http://127.0.0.1:8000/api/villes');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Villes récupérées\n";
    echo "   Total: " . count($data ?? []) . "\n\n";
} else {
    echo "❌ Erreur villes (code: $httpCode)\n";
    echo "   Réponse: $response\n\n";
}

// Test 6: Entités
echo "6. Test /api/entites...\n";
$ch = curl_init('http://127.0.0.1:8000/api/entites');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Entités récupérées\n";
    echo "   Total: " . count($data ?? []) . "\n\n";
} else {
    echo "❌ Erreur entités (code: $httpCode)\n";
    echo "   Réponse: $response\n\n";
}

echo "=== FIN DES TESTS ===\n";
echo "\nSi tous les tests sont ✅, l'API fonctionne correctement.\n";
echo "Le problème vient probablement du frontend (Nuxt).\n";
