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
$csrfToken = $dependencies[GlobalValues::CSRF_TOKEN];

$userController = new UserController($responseFactory, $twig, $baseURL, $gCsrfToken, $googleClientId);
$courseController = new CourseController($responseFactory, $twig, $baseURL, $csrfToken, $gCsrfToken);

$app->group('/user', function (RouteCollectorProxy $group) use ($userController, $courseController) {
  $group->get('/course/{course_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->coursePage($request, $response, $args);
  });

  $group->get('/logout', function ($request, $response, $args) use ($userController) {
    return $userController->logout($request, $response, $args);
  });

  $group->get('/course/topic/{course_id}/{topic_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->courseTopicPage($request, $response, $args);
  });

  $group->get('/course/quiz/{course_id}/{quiz_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->courseQuizPage($request, $response, $args);
  });

  // send form
  $group->post('/course/quiz', function ($request, $response, $args) use ($courseController) {
    return $courseController->processQuizRequest($request, $response, $args);
  });
})->add(function ($request, $handler) {
  return checkUserTokenMiddleware($request, $handler);
});
