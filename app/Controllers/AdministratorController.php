<?php

namespace app\Controllers;

use app\Models\AdministratorModel;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AdministratorController extends Controller
{

  public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
  {
    $this->view('/pages/administrators/login.html.twig', []);

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
      $this->view('/pages/administrators/login.html.twig', [
        'email' => $administratorByEmail->email,
        'password' => $administratorByEmail->password
      ]);
    }

    return $response;
  }
}