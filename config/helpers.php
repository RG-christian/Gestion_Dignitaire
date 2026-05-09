<?php
// config/helpers.php

/**
 * Échappe et affiche une valeur de manière sécurisée
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirige vers une URL
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

/**
 * Redirige vers un contrôleur/action
 */
function redirectTo(string $controller, string $action = 'index', ?int $id = null): void
{
    $url = "index.php?controller=$controller&action=$action";
    if ($id !== null) {
        $url .= "&id=$id";
    }
    redirect($url);
}

/**
 * Retourne l'URL d'un contrôleur/action
 */
function url(string $controller, string $action = 'index', ?int $id = null): string
{
    $url = "index.php?controller=$controller&action=$action";
    if ($id !== null) {
        $url .= "&id=$id";
    }
    return $url;
}

/**
 * Affiche un message flash stocké en session
 */
function flash(string $key = 'message'): ?string
{
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return null;
}

/**
 * Définit un message flash
 */
function setFlash(string $message, string $key = 'message'): void
{
    $_SESSION[$key] = $message;
}

/**
 * Vérifie si une valeur existe dans un tableau POST
 */
function old(string $key, $default = ''): string
{
    return $_POST[$key] ?? $default;
}

/**
 * Formate une date
 */
function formatDate(?string $date, string $format = 'd/m/Y'): string
{
    if (empty($date)) {
        return '';
    }
    $dt = new DateTime($date);
    return $dt->format($format);
}

/**
 * Vérifie si l'utilisateur a un rôle spécifique
 */
function hasRole(string $role): bool
{
    return isset($_SESSION['role_name']) && $_SESSION['role_name'] === $role;
}

/**
 * Vérifie si l'utilisateur a accès à une fonction
 */
function hasFunction(string $fonction): bool
{
    if (!isset($_SESSION['fonctions'])) {
        return false;
    }
    foreach ($_SESSION['fonctions'] as $f) {
        if ($f['fonction_name'] === $fonction) {
            return true;
        }
    }
    return false;
}

/**
 * Génère un attribut HTML selected si la condition est vraie
 */
function selected($value1, $value2): string
{
    return $value1 == $value2 ? 'selected' : '';
}

/**
 * Génère un attribut HTML checked si la condition est vraie
 */
function checked($value1, $value2): string
{
    return $value1 == $value2 ? 'checked' : '';
}

/**
 * Retourne la taille d'un fichier formatée
 */
function formatFileSize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Génère un jeton aléatoire
 */
function generateToken(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

/**
 * Vérifie si la requête est en POST
 */
function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Vérifie si la requête est en GET
 */
function isGet(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Retourne l'IP du client
 */
function getClientIp(): string
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}
