<?php

require_once __DIR__ . '/../functions/authentication.php';

use app\classes\GlobalValues;
use app\Controllers\UserController;
use app\Controllers\CourseController;
use app\Models\CourseModel;
use Slim\Routing\RouteCollectorProxy;

$courseModel = new CourseModel();
$courses = $courseModel->all();

$googleClientId = $_ENV['GOOGLE_CLIENT_ID'];

$responseFactory = $dependencies['response_factory'];
$twig = $dependencies['twig'];
$baseURL = $dependencies['base_url'];
$gCsrfToken = $dependencies[GlobalValues::G_CSRF_TOKEN];
$csrfToken = $dependencies[GlobalValues::CSRF_TOKEN];

$userController = new UserController($responseFactory, $twig, $baseURL, $gCsrfToken, $googleClientId);
$courseController = new CourseController($responseFactory, $twig, $baseURL, $csrfToken, $gCsrfToken);

$app->get('/', function ($request, $response, $args) use ($courseController) {
  return $courseController->index($request, $response, $args);
});

$app->get('', function ($request, $response, $args) use ($courseController) {
  return $courseController->index($request, $response, $args);
});

$app->group('/', function (RouteCollectorProxy $group) use ($twig, $courseController, $courses) {
  $group->get('terms-and-conditions', function ($request, $response, $args) use ($twig, $courses) {
    return $twig->render($response, '/pages/others/terms_and_conditions.html.twig', [
      'courses' => $courses
    ]);
  });

  $group->get('privacy-policy', function ($request, $response, $args) use ($twig, $courses) {
    return $twig->render($response, '/pages/others/privacy_policy.html.twig', [
      'user_is_connected' => $_SESSION[GlobalValues::USER_IS_CONNECTED],
      'courses' => $courses
    ]);
  });

  $group->get('about', function ($request, $response, $args) use ($twig, $courses) {
    return $twig->render($response, '/pages/others/about.html.twig', [
      'user_is_connected' => $_SESSION[GlobalValues::USER_IS_CONNECTED],
      'courses' => $courses
    ]);
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
