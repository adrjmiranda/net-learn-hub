<?php

namespace app\Controllers;

use app\classes\CourseMessage;
use app\classes\GlobalValues;
use app\classes\UserMessage;
use app\Models\CourseModel;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class CourseController extends Controller
{
  protected ResponseFactoryInterface $responseFactory;
  protected Twig $twig;
  private array $data;
  private string $path;

  public function __construct(ResponseFactoryInterface $responseFactory, Twig $twig, string $baseURL, string $csrfToken)
  {
    $this->responseFactory = $responseFactory;
    $this->twig = $twig;

    $this->model = new CourseModel();

    $this->path = '/pages/courses/';

    $this->data = [];
    $this->data['base_url'] = $baseURL;
    $this->data['csrf_token'] = $csrfToken;
  }

  public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $this->path .= 'create_course.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['title'] = '';
    $this->data['workload'] = '';
    $this->data['description'] = '';
    $this->data['err_image'] = false;
    $this->data['err_title'] = false;
    $this->data['err_workload'] = false;
    $this->data['err_description'] = false;
    $this->data['session_message'] = '';
    $this->data['message_type'] = '';

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processStoreRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();

    $uploadedImage = $_FILES['image'] ?? [];
    $title = $params['title'];
    $workload = $params['workload'];
    $description = $params['description'];

    $imagesTypes = ['image/jpeg', 'image/jpg', 'image/png'];

    $imageType = $uploadedImage['type'];

    $this->path .= 'create_course.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['title'] = $title;
    $this->data['workload'] = $workload;
    $this->data['description'] = $description;
    $this->data['err_image'] = false;
    $this->data['err_title'] = false;
    $this->data['err_workload'] = false;
    $this->data['err_description'] = false;
    $this->data['session_message'] = '';
    $this->data['message_type'] = GlobalValues::TYPE_MSG_ERROR;

    if ($_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID]) {
      $message = UserMessage::INVALID_CSRF_TOKEN;
    } elseif ($uploadedImage === null || $uploadedImage['error'] !== UPLOAD_ERR_OK || !in_array($imageType, $imagesTypes)) {
      $this->data['err_image'] = true;
      $message = CourseMessage::ERR_INVALID_IMAGE_TYPE;
    } elseif (!isValidBlob(file_get_contents($uploadedImage['tmp_name']), 'image')) {
      $this->data['err_image'] = true;
      $message = CourseMessage::ERR_INVALID_IMAGE_LENGTH;
    } elseif (!isValidText($title, 'title')) {
      $this->data['err_title'] = true;
      $message = CourseMessage::ERR_INVALID_TITLE;
    } elseif (!isValidWorkLoad($workload)) {
      $this->data['err_workload'] = true;
      $message = CourseMessage::ERR_INVALID_WORKLOAD;
    } elseif (!isValidText($description, 'description')) {
      $this->data['err_description'] = true;
      $message = CourseMessage::ERR_INVALID_DESCRIPTION;
    } else {
      $this->data['err_image'] = false;
      $this->data['err_title'] = false;
      $this->data['err_workload'] = false;
      $this->data['err_description'] = false;

      $courseByTitle = $this->model->getByTitle($title);
      $image = file_get_contents($uploadedImage['tmp_name']);

      if ($courseByTitle) {
        $this->data['err_title'] = true;
        $message = CourseMessage::ERR_TITLE_ALREADY_EXISTS;
      } elseif ($this->model->store($image, $title, $workload, $description)) {
        $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_CREATE;
        $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $message = CourseMessage::ERR_FAIL_CREATE;
      }
    }

    $this->data['session_message'] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }
}