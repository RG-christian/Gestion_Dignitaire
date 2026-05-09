<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Chargement manuel des fichiers de configuration
// IMPORTANT : Charger security.php EN PREMIER

require_once 'config/security.php';
require_once 'config/validator.php';
require_once 'config/logger.php';
require_once 'config/upload.php';
require_once 'config/helpers.php';
require_once 'config/database.php';

// Initialiser la session sécurisée
secureSession();

// Utiliser la classe Router pour gérer toutes les requêtes
use routers\Router;

// Inclusion de la classe Router
require_once 'routers/Router.class.php';

// Création du routeur et gestion de la requête courante
$router = new Router();
$router->handleRequest();
