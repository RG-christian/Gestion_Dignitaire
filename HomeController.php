<?php
$hash = '$2y$10$8dlIIKUNbqblTH6ap3IItuliYcSarnrJqnzunfs6C5bbZ8dsV7hc6';
if (password_verify('admin123', $hash)) {
    echo "Mot de passe VALIDE";
} else {
    echo "Mot de passe INVALIDE";
}
