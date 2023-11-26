<?php

require_once __DIR__ . '/../functions/authentication.php';

use app\Controllers\CourseController;
use Slim\Routing\RouteCollectorProxy;
use app\Controllers\AdministratorController;

$responseFactory = $dependencies['response_factory'];
$twig = $dependencies['twig'];
$baseURL = $dependencies['base_url'];
$csrfToken = $dependencies['csrf_token'];

$administratorController = new AdministratorController($responseFactory, $twig, $baseURL, $csrfToken);
$courseController = new CourseController($responseFactory, $twig, $baseURL, $csrfToken);

$table = $administratorController->getTable();

$app->group('/admin', function (RouteCollectorProxy $group) use ($administratorController, $courseController) {
  $group->get('/login', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->index($request, $response, $args);
  });

  $group->post('/login', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->login($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->get('/dashboard', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->dashboard($request, $response, $args);
  });

  $group->get('/logout', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->logout($request, $response, $args);
  });

  // course
  $group->get('/course/create', function ($request, $response, $args) use ($courseController) {
    return $courseController->create($request, $response, $args);
  });

  $group->post('/course/create', function ($request, $response, $args) use ($courseController) {
    return $courseController->store($request, $response, $args);
  });
})->add(function ($request, $handler) use ($table) {
  return verifyTokenMiddleware($request, $handler, $table);
});
