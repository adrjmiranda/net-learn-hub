<?php

namespace app\Controllers;

use app\Models\CourseModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CourseController extends Controller
{
  public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $course = new CourseModel();
    $courses = $course->all();

    $this->view('/pages/courses/home.html.twig', [
      'courses' => $courses
    ]);

    return $response;
  }
}