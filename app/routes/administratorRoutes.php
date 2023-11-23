<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Psr7\Response as SlimResponse;

$checkIfYouAreLoggedIn = function (Request $request, RequestHandler $handler) {
  $response = $handler->handle($request);
  $session = $_SESSION ?? [];

  if (isset($session['adminToken'])) {
    return $response->withHeader('Location', '/admin/dashboard')->withStatus(302);
  }

  return $handler->handle($request);
};

$verifyTokenMiddleware = function (Request $request, RequestHandler $handler) {
  $response = $handler->handle($request);
  $session = $_SESSION ?? [];

  if (!isset($session['adminToken'])) {
    return $response->withHeader('Location', '/admin/login')->withStatus(302);
  }

  return $handler->handle($request);
};

$app->group('/admin', function (RouteCollectorProxy $group) {
  $group->get('/login', 'app\Controllers\AdministratorController:index');
  $group->post('/login', 'app\Controllers\AdministratorController:login');
})->add(function (Request $request, RequestHandler $handler) {
  if (isset($_SESSION['adminToken'])) {
    return (new SlimResponse())
      ->withHeader('Location', '/admin/dashboard')
      ->withStatus(302);
  }

  return $handler->handle($request);
});

$app->group('/admin', function (RouteCollectorProxy $group) {
  $group->get('/dashboard', 'app\Controllers\AdministratorController:dashboard');
});