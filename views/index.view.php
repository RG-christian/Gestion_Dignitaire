

<?php
// Pour générer un mot de passe haché sécurisé (à insérer dans la base)
$password = 'admin';  // Ton mot de passe en clair
$hash = password_hash($password, PASSWORD_BCRYPT);
// Puis tu utilises ce $hash dans l’insertion SQL ci-dessous

echo $hash;