<?php

namespace app\Controllers;

use app\Models\AdministratorModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AdministratorController extends Controller
{
  public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $this->view('/pages/administrators/login.html.twig', []);

    return $response;
  }

  public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    // try login

    $this->view('/pages/administrators/login.html.twig', []);

    return $response;
  }
}