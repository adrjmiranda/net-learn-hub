<?php

namespace app\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CourseController extends Controller
{
  public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $this->view('/pages/courses/home.twig', []);

    return $response;
  }
}