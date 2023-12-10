<?php

require_once __DIR__ . '/../functions/authentication.php';

use app\classes\GlobalValues;
use app\Controllers\CourseController;
use Slim\Routing\RouteCollectorProxy;
use app\Controllers\UserController;

$googleClientId = $_ENV['GOOGLE_CLIENT_ID'];

$responseFactory = $dependencies['response_factory'];
$twig = $dependencies['twig'];
$baseURL = $dependencies['base_url'];
$gCsrfToken = $dependencies[GlobalValues::G_CSRF_TOKEN];

$administratorController = new UserController($responseFactory, $twig, $baseURL, $gCsrfToken, $googleClientId);
$courseController = new CourseController($responseFactory, $twig, $baseURL, $csrfToken);

$app->group('/user', function (RouteCollectorProxy $group) use ($courseController) {
  $group->get('/course/{course_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->coursePage($request, $response, $args);
  });
})->add(function ($request, $handler) {
  return checkUserTokenMiddleware($request, $handler);
});
