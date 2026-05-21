<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

try {
    // Trouver l'utilisateur admin1
    $user = User::where('username', 'admin1')->first();

    if (!$user) {
        echo "❌ Utilisateur admin1 non trouvé\n";
        exit(1);
    }

    // Réinitialiser le mot de passe à 'admin123'
    $user->password = Hash::make('admin123');
    $user->save();

    echo "✅ Mot de passe réinitialisé avec succès pour admin1\n";
    echo "Username: admin1\n";
    echo "Password: admin123\n";

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
