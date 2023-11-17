<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

$baseURL = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI'] . '?');

// dotenv config
$path = dirname(__FILE__, 1);
$dotenv = Dotenv::createImmutable($path);
$dotenv->load();

// twig config
$loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__) . '/app/Views');
$twig = new \Twig\Environment($loader);

// slim config
$app = AppFactory::create();

return [
  'baseURL' => $baseURL,
  'dotenv' => $dotenv,
  'twig' => $twig,
  'slim_app' => $app
];