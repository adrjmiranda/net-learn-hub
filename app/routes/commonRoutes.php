<?php

require_once __DIR__ . '/../functions/authentication.php';

use app\classes\GlobalValues;
use app\Controllers\UserController;
use app\Controllers\CourseController;
use Slim\Routing\RouteCollectorProxy;

$googleClientId = $_ENV['GOOGLE_CLIENT_ID'];

$responseFactory = $dependencies['response_factory'];
$twig = $dependencies['twig'];
$baseURL = $dependencies['base_url'];
$gCsrfToken = $dependencies[GlobalValues::G_CSRF_TOKEN];
$csrfToken = $dependencies[GlobalValues::CSRF_TOKEN];

$userController = new UserController($responseFactory, $twig, $baseURL, $gCsrfToken, $googleClientId);
$courseController = new CourseController($responseFactory, $twig, $baseURL, $csrfToken, $gCsrfToken);

$app->group('/', function (RouteCollectorProxy $group) use ($twig, $courseController) {
  $group->get('terms-and-conditions', function ($request, $response, $args) use ($twig) {
    return $twig->render($response, '/pages/others/terms_and_conditions.html.twig', []);
  });

  $group->get('privacy-policy', function ($request, $response, $args) use ($twig) {
    return $twig->render($response, '/pages/others/privacy_policy.html.twig', []);
  });

  $group->get('home', function ($request, $response, $args) use ($courseController) {
    return $courseController->index($request, $response, $args);
  });
});


$app->group('/user', function (RouteCollectorProxy $group) use ($userController) {
  $group->get('/login', function ($request, $response, $args) use ($userController) {
    return $userController->login($request, $response, $args);
  });

  $group->post('/login', function ($request, $response, $args) use ($userController) {
    return $userController->auth($request, $response, $args);
  });
})->add(function ($request, $handler) {
  return checkUserTokenMiddleware($request, $handler);
});
