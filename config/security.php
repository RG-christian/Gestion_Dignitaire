<?php
// config/security.php

/**
 * Génère un token CSRF et le stocke en session
 */
function generateCSRFToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie la validité du token CSRF
 */
function verifyCSRFToken(?string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Génère un champ input hidden avec le token CSRF
 */
function csrfField(): string
{
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Sécurise les sessions
 */
function secureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        // Configuration sécurisée des sessions
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_secure', '0'); // Mettre à 1 si HTTPS
        ini_set('session.cookie_samesite', 'Strict');
        
        session_start();
        
        // Régénération de l'ID de session pour éviter la fixation
        if (!isset($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
        
        // Timeout de session (30 minutes)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            session_unset();
            session_destroy();
            session_start();
        }
        $_SESSION['last_activity'] = time();
    }
}

/**
 * Vérifie si l'utilisateur est authentifié
 */
function isAuthenticated(): bool
{
    return isset($_SESSION['admin_id']);
}

/**
 * Redirige vers la page de login si non authentifié
 */
function requireAuth(): void
{
    if (!isAuthenticated()) {
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}
