<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\classes\GlobalValues;

// session config
if (!isset($_SESSION)) {
  // TODO: define in the production enviroment
  // session_set_cookie_params([
  //   'lifetime' => 7200,
  //   'path' => '/',
  //   'domain' => 'seusite.com',
  //   'secure' => true,
  //   'httponly' => true
  // ]);

  session_start();
}


use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

if (!isset($_SESSION[GlobalValues::CSRF_TOKEN])) {
  $csrf_token = bin2hex(random_bytes(32));
  $_SESSION[GlobalValues::CSRF_TOKEN] = $csrf_token;
}

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