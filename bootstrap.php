<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/functions/helpers.php';

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

// dotenv config
$path = dirname(__FILE__, 1);
$dotenv = Dotenv::createImmutable($path);
$dotenv->load();

// slim config
$app = AppFactory::create();

return [
  'slim_app' => $app
];
