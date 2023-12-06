<?php

namespace app\Controllers;

use app\classes\GlobalValues;
use app\classes\UserMessage;
use app\Models\CourseModel;
use app\Models\UserModel;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

class UserController {
  protected ResponseFactoryInterface $responseFactory;
  protected Twig $twig;
  private array $data;
  private string $path;

  public function __construct(ResponseFactoryInterface $responseFactory, Twig $twig, string $baseURL, string $csrfToken) {
    $this->responseFactory = $responseFactory;
    $this->twig = $twig;

    $this->model = new UserModel();

    $this->path = '/pages/users/';

    $this->data = [];
    $this->data['base_url'] = $baseURL;
    $this->data['csrf_token'] = $csrfToken;
  }
}