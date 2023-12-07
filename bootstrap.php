<?php

require_once __DIR__.'/vendor/autoload.php';

use app\classes\GlobalValues;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

// session config
if(!isset($_SESSION)) {
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

$_SESSION[GlobalValues::ADMIN_ID_IDENTIFIER] ??= '';

// CSRF TOKEN
if(!isset($_SESSION[GlobalValues::CSRF_TOKEN])) {
  $csrf_token = bin2hex(random_bytes(32));
  $_SESSION[GlobalValues::CSRF_TOKEN] = $csrf_token;
}

$_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID] ??= true;

// GCSRF TOKEN
$_SESSION[GlobalValues::G_CSRF_TOKEN] ??= '';

$_SESSION[GlobalValues::G_CSRF_TOKEN_IS_INVALID] ??= true;

// message config
$_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] ??= '';
$_SESSION[GlobalValues::SESSION_MESSAGE] ??= '';
$_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ??= '';

$_SESSION[GlobalValues::SESSION_MESSAGE] = $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT];

if($_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] !== '') {
  $_SESSION[GlobalValues::SESSION_MESSAGE] = $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT];
  $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = '';
}

// TODO: define the environment
// in development environment
$baseURL = 'http://'.$_SERVER['SERVER_NAME'];
// in production environment
// $baseURL = 'https://' . $_SERVER['SERVER_NAME'];

// dotenv config
$path = dirname(__FILE__, 1);
$dotenv = Dotenv::createImmutable($path);
$dotenv->load();

// slim config
$app = AppFactory::create();

// Twig Configuration
$twig = Twig::create($path.'/app/Views/', []);

// Added Twig-View middleware
$app->add(TwigMiddleware::create($app, $twig));

return [
  'base_url' => $baseURL,
  'slim_app' => $app,
  'twig' => $twig,
  'response_factory' => $app->getResponseFactory(),
  'csrf_token' => $_SESSION[GlobalValues::CSRF_TOKEN]
];