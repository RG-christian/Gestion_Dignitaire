<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


// On utilise la classe Router pour gérer toutes les requêtes de l'application
use routers\Router;
// Inclusion de la classe Router
require_once 'routers/Router.class.php';

// Création du routeur et gestion de la requête courante
$router = new Router();
$router->handleRequest();

