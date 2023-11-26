<?php

require_once __DIR__ . '/../functions/authentication.php';

use Slim\Routing\RouteCollectorProxy;
use app\Controllers\AdministratorController;

$responseFactory = $dependencies['response_factory'];
$twig = $dependencies['twig'];
$baseURL = $dependencies['base_url'];
$csrfToken = $dependencies['csrf_token'];

$controller = new AdministratorController($responseFactory, $twig, $baseURL, $csrfToken);
$table = $controller->getTable();

$app->group('/admin', function (RouteCollectorProxy $group) use ($controller) {
  $group->get('/login', function ($request, $response, $args) use ($controller) {
    return $controller->index($request, $response, $args);
  });

  $group->post('/login', function ($request, $response, $args) use ($controller) {
    return $controller->login($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->get('/dashboard', function ($request, $response, $args) use ($controller) {
    return $controller->dashboard($request, $response, $args);
  });

  $group->get('/logout', function ($request, $response, $args) use ($controller) {
    return $controller->logout($request, $response, $args);
  });

})->add(function ($request, $handler) use ($table) {
  return verifyTokenMiddleware($request, $handler, $table);
});
