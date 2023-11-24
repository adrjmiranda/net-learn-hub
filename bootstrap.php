<?php

// session config
// TODO: define in the production enviroment
if (!isset($_SESSION)) {
  // session_set_cookie_params([
  //   'lifetime' => 7200,
  //   'path' => '/',
  //   'domain' => 'seusite.com',
  //   'secure' => true,
  //   'httponly' => true
  // ]);

  session_start();
}

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// TODO: define the environment
// in development environment
$baseURL = 'http://' . $_SERVER['SERVER_NAME'];
// in production environment
// $baseURL = 'https://' . $_SERVER['SERVER_NAME'];

// dotenv config
$path = dirname(__FILE__, 1);
$dotenv = Dotenv::createImmutable($path);
$dotenv->load();

// slim config
$app = AppFactory::create();

return [
  'base_url' => $baseURL,
  'slim_app' => $app
];