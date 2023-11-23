<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy;

// Middleware para verificar o token na sessão
$verifyTokenMiddleware = function (Request $request, RequestHandler $handler) {
  // Verifique se o token está presente na sessão ou não
  $session = $_SESSION ?? [];
  if (!isset($session['adminToken'])) {
    // Token não está presente na sessão, redirecione ou realize outras ações necessárias
    $response = $handler->handle($request);
    return $response->withHeader('Location', '/login')->withStatus(302);
  }

  // Token está presente na sessão, continue com a requisição
  return $handler->handle($request);
};

$app->get('/admin', 'app\Controllers\AdministratorController:index');
$app->post('/admin/login', 'app\Controllers\AdministratorController:login');

$app->group('/admin', function (RouteCollectorProxy $group) {
  $group->get('/dashboard', function (Request $request, Response $response, array $args) {
    return $response;
  });
});