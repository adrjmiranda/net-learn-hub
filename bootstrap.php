<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/functions/helpers.php';

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;


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
  'baseURL' => $baseURL,
  'slim_app' => $app
];
