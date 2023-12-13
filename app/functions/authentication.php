<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use app\classes\GlobalValues;

function administratorAuthentication(string $password, string $hash, string $table): bool
{
  $auth = false;

  if (password_verify($password, $hash)) {
    $token = bin2hex(random_bytes(32));

    switch ($table) {
      case GlobalValues::ADMINISTRATORS_TABLE:
        if (!isset($_SESSION[GlobalValues::ADMIN_TOKEN])) {
          $_SESSION[GlobalValues::ADMIN_TOKEN] = $token;
        }
        break;
    }

    if (isset($_SESSION[GlobalValues::ADMIN_TOKEN])) {
      $auth = true;
    }
  }

  return $auth;
}

function checkAdminTokenMiddleware(Request $request, RequestHandler $handler)
{
  $session = $_SESSION ?? [];
  $response = new SlimResponse();
  $currentPath = $request->getUri()->getPath();

  if (!isset($session[GlobalValues::ADMIN_TOKEN]) && $currentPath !== '/admin/login') {
    return $response->withHeader('Location', '/admin/login')->withHeader('Allow', 'GET')->withStatus(302);
  }

  if (isset($session[GlobalValues::ADMIN_TOKEN]) && $currentPath === '/admin/login') {
    return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
  }

  return $handler->handle($request);
}

function checkUserTokenMiddleware(Request $request, RequestHandler $handler)
{
  $session = $_SESSION ?? [];
  $response = new SlimResponse();
  $currentPath = $request->getUri()->getPath();

  if (!isset($session[GlobalValues::USER_TOKEN]) && $currentPath !== '/user/login' && $currentPath !== '/home') {
    return $response->withHeader('Location', '/user/login')->withHeader('Allow', 'GET')->withStatus(302);
  }

  if (isset($session[GlobalValues::USER_TOKEN]) && $currentPath === '/user/login') {
    return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
  }


  return $handler->handle($request);
}

function verifyCSRFToken(Request $request, RequestHandler $handler)
{
  $session = $_SESSION ?? [];
  $params = $request->getParsedBody();

  $_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID] ??= true;

  if (isset($params[GlobalValues::CSRF_TOKEN])) {
    $csrfToken = $params[GlobalValues::CSRF_TOKEN];

    if ($csrfToken === $session[GlobalValues::CSRF_TOKEN]) {
      $_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID] = false;
    }
  }

  return $handler->handle($request);
}

function verifyGCSRFToken(Request $request, RequestHandler $handler)
{
  $session = $_SESSION ?? [];
  $params = $request->getParsedBody();

  $_SESSION[GlobalValues::G_CSRF_TOKEN_IS_INVALID] ??= true;

  if (isset($params[GlobalValues::G_CSRF_TOKEN])) {
    $gCsrfToken = $params[GlobalValues::G_CSRF_TOKEN];

    if ($gCsrfToken === $session[GlobalValues::G_CSRF_TOKEN]) {
      $_SESSION[GlobalValues::G_CSRF_TOKEN_IS_INVALID] = false;
    }
  }

  return $handler->handle($request);
}