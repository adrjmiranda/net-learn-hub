<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

function authentication(string $password, string $hash, string $table): void
{
  if (password_verify($password, $hash)) {
    $token = bin2hex(random_bytes(32));

    switch ($table) {
      case 'administrators':
        if (!isset($_SESSION['admin_token'])) {
          $_SESSION['admin_token'] = $token;
        }
        break;

      case 'users':
        if (!isset($_SESSION['user_token'])) {
          $_SESSION['user_token'] = $token;
        }
        break;

      default:
        return;
    }
  }
}

function verifyTokenMiddleware(Request $request, RequestHandler $handler, string $tableName)
{
  $session = $_SESSION ?? [];
  $response = new SlimResponse();
  $currentPath = $request->getUri()->getPath();

  $tokenType = null;

  switch ($tableName) {
    case 'administrators':
      $tokenType = 'admin_token';
      break;

    case 'users':
      $tokenType = 'user_token';
      break;

    default:
      $tokenType = '';
      break;
  }

  if (!isset($session[$tokenType]) && $currentPath !== '/admin/login') {
    return $response->withHeader('Location', '/admin/login')->withStatus(302);
  }

  if (isset($session[$tokenType]) && $currentPath === '/admin/login') {
    return $response->withHeader('Location', '/admin/dashboard')->withStatus(302);
  }

  return $handler->handle($request);
}