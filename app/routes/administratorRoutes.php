<?php

require_once __DIR__ . '/../functions/authentication.php';

use app\classes\GlobalValues;
use app\Controllers\CourseController;
use app\Controllers\UserController;
use Slim\Routing\RouteCollectorProxy;
use app\Controllers\AdministratorController;

$googleClientId = $_ENV['GOOGLE_CLIENT_ID'];

$responseFactory = $dependencies['response_factory'];
$twig = $dependencies['twig'];
$baseURL = $dependencies['base_url'];
$gCsrfToken = $dependencies[GlobalValues::G_CSRF_TOKEN];
$csrfToken = $dependencies[GlobalValues::CSRF_TOKEN];

$administratorController = new AdministratorController($responseFactory, $twig, $baseURL, $csrfToken);
$userController = new UserController($responseFactory, $twig, $baseURL, $gCsrfToken, $googleClientId);
$courseController = new CourseController($responseFactory, $twig, $baseURL, $csrfToken, $gCsrfToken);

$app->group('/admin', function (RouteCollectorProxy $group) use ($administratorController, $userController, $courseController) {
  $group->get('/login', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->index($request, $response, $args);
  });

  $group->get('/dashboard', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->dashboard($request, $response, $args);
  });

  $group->get('/settings', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->settings($request, $response, $args);
  });

  $group->get('/logout', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->logout($request, $response, $args);
  });

  $group->get('/course/create', function ($request, $response, $args) use ($courseController) {
    return $courseController->create($request, $response, $args);
  });

  $group->get('/course/edit/{id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->edit($request, $response, $args);
  });

  $group->get('/course/topics/{course_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->topics($request, $response, $args);
  });

  $group->get('/course/topics/create/{course_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->createTopic($request, $response, $args);
  });

  $group->get('/course/topics/edit/{course_id}/{topic_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->editTopic($request, $response, $args);
  });

  $group->get('/course/quizzes/{course_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->quizzes($request, $response, $args);
  });

  $group->get('/course/quizzes/create/{course_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->createQuiz($request, $response, $args);
  });

  $group->get('/course/quizzes/edit/{course_id}/{quiz_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->editQuiz($request, $response, $args);
  });

  $group->get('/course/quizzes/questions/{course_id}/{quiz_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->questions($request, $response, $args);
  });

  $group->get('/course/quizzes/questions/create/{course_id}/{quiz_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->createQuestion($request, $response, $args);
  });

  $group->get('/course/quizzes/questions/edit/{course_id}/{quiz_id}/{question_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->editQuestion($request, $response, $args);
  });

  $group->get('/users/dashboard', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->userDashboard($request, $response, $args);
  });

  // send form
  $group->post('/login', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->login($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->post('/settings/edit', function ($request, $response, $args) use ($administratorController) {
    return $administratorController->processUpdateRequest($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->post('/course/create', function ($request, $response, $args) use ($courseController) {
    return $courseController->processStoreRequest($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->post('/course/edit', function ($request, $response, $args) use ($courseController) {
    return $courseController->processUpdateRequest($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->post('/course/topics/create', function ($request, $response, $args) use ($courseController) {
    return $courseController->processTopicStoreRequest($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->post('/course/topics/edit', function ($request, $response, $args) use ($courseController) {
    return $courseController->processTopicUpdateRequest($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->post('/course/quizzes/create', function ($request, $response, $args) use ($courseController) {
    return $courseController->processQuizStoreRequest($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->post('/course/quizzes/edit', function ($request, $response, $args) use ($courseController) {
    return $courseController->processQuizUpdateRequest($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->post('/course/quizzes/questions/create', function ($request, $response, $args) use ($courseController) {
    return $courseController->processQuestionStoreRequest($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  $group->post('/course/quizzes/questions/edit', function ($request, $response, $args) use ($courseController) {
    return $courseController->processQuestionUpdateRequest($request, $response, $args);
  })->add(function ($request, $handler) {
    return verifyCSRFToken($request, $handler);
  });

  // delete
  $group->get('/course/delete/{course_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->processDeleteRequest($request, $response, $args);
  });

  $group->get('/course/topics/delete/{course_id}/{topic_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->processTopicDeleteRequest($request, $response, $args);
  });

  $group->get('/course/quizzes/delete/{course_id}/{quiz_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->processQuizDeleteRequest($request, $response, $args);
  });

  $group->get('/course/quizzes/questions/delete/{course_id}/{quiz_id}/{question_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->processQuestionDeleteRequest($request, $response, $args);
  });

  $group->get('/users/delete/{ user_id }', function ($request, $response, $args) use ($userController) {
    return $userController->processUserDeleteRequest($request, $response, $args);
  });

  // activate/deactivate to courses and quizzes
  $group->get('/course/visibility/{course_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->processVisibilityRequest($request, $response, $args);
  });

  $group->get('/course/quiz/visibility/{course_id}/{quiz_id}', function ($request, $response, $args) use ($courseController) {
    return $courseController->processQuizVisibilityRequest($request, $response, $args);
  });
})->add(function ($request, $handler) {
  return checkAdminTokenMiddleware($request, $handler);
});
