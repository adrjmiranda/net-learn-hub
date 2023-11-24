<?php

namespace app\Controllers;

use app\Models\AdministratorModel;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AdministratorController extends Controller
{

  public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
  {
    $this->view('/pages/administrators/login.html.twig', [
      'page_title' => 'NetLearnHub | Aprenda de graça TI'
    ]);
    return $response;
  }

  public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
  {
    $params = $request->getParsedBody();

    $email = $params['email'];
    $password = $params['password'];

    $administrator = new AdministratorModel();
    $administratorByEmail = $administrator->getUserByEmail($email);

    if (!empty($administratorByEmail)) {
      $administrator->authUser($password, $administratorByEmail->password);
    }

    return $response->withHeader('Location', '/admin/dashboard')->withStatus(302);
  }

  public function dashboard(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
  {
    $this->view('/pages/administrators/dashboard.html.twig', [
      'page_title' => 'NetLearnHub | Aprenda de graça TI'
    ]);
    return $response;
  }

  public function logout(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
  {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }

    session_destroy();

    return $response->withHeader('Location', '/admin/login')->withStatus(302);
  }
}