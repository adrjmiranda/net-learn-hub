<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use app\classes\GlobalValues;

function authentication(string $password, string $hash, string $table): void
{
  if (password_verify($password, $hash)) {
    $token = bin2hex(random_bytes(32));

    switch ($table) {
      case GlobalValues::ADMINISTRATORS_TABLE:
        if (!isset($_SESSION[GlobalValues::ADMIN_TOKEN])) {
          $_SESSION[GlobalValues::ADMIN_TOKEN] = $token;
        }
        break;

      case GlobalValues::USERS_TABLE:
        if (!isset($_SESSION[GlobalValues::USER_TOKEN])) {
          $_SESSION[GlobalValues::USER_TOKEN] = $token;
        }
        break;

      default:
        return;
    }
  }
}

function verifyTokenMiddleware(Request $request, RequestHandler $handler, string $table)
{
  $session = $_SESSION ?? [];
  $response = new SlimResponse();
  $currentPath = $request->getUri()->getPath();

  $tokenName = '';

  switch ($table) {
    case GlobalValues::ADMINISTRATORS_TABLE:
      $tokenName = GlobalValues::ADMIN_TOKEN;
      break;

    case GlobalValues::USERS_TABLE:
      $tokenName = GlobalValues::USER_TOKEN;
      break;
  }

  if (!isset($session[$tokenName]) && $currentPath !== '/admin/login') {
    return $response->withHeader('Location', '/admin/login')->withStatus(302);
  }

  if (isset($session[$tokenName]) && $currentPath === '/admin/login') {
    return $response->withHeader('Location', '/admin/dashboard')->withStatus(302);
  }

  return $handler->handle($request);
}