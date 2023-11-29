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
    $this->data['description'] = '';
    $this->data['err_image'] = false;
    $this->data['err_title'] = false;
    $this->data['err_description'] = false;
    $this->data['session_message'] = '';
    $this->data['message_type'] = 'error';

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function store(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();

    $uploadedImage = $request->getUploadedFiles()['image'] ?? null;
    $title = $params['title'];
    $description = $params['description'];

    $imagesTypes = ['image/jpeg', 'image/jpg', 'image/png'];

    $imageType = $uploadedImage->getClientMediaType();
    $imageSize = $uploadedImage->getSize();

    $this->path .= 'create_course.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['title'] = $title;
    $this->data['description'] = $description;
    $this->data['err_image'] = false;
    $this->data['err_title'] = false;
    $this->data['err_description'] = false;
    $this->data['session_message'] = '';
    $this->data['message_type'] = 'error';

    if ($_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID]) {
      $this->data['session_message'] = UserMessage::INVALID_CSRF_TOKEN;
    } elseif ($uploadedImage === null || $uploadedImage->getError() !== UPLOAD_ERR_OK || !in_array($imageType, $imagesTypes)) {
      $this->data['err_image'] = true;
      $this->data['session_message'] = CourseMessage::ERR_INVALID_IMAGE_TYPE;
    } elseif ($imageSize > GlobalValues::MAXIMUM_SIZE_OF_THE_IMAGE_BLOB) {
      $this->data['err_image'] = true;
      $this->data['session_message'] = CourseMessage::ERR_INVALID_IMAGE_LENGTH;
    } elseif (!isValidText($title, 'title')) {
      $this->data['err_title'] = true;
      $this->data['session_message'] = CourseMessage::ERR_INVALID_TITLE;
    } elseif (!isValidText($description, 'description')) {
      $this->data['err_description'] = true;
      $this->data['session_message'] = CourseMessage::ERR_INVALID_DESCRIPTION;
    } else {
      $imageData = $uploadedImage->getStream()->getContents();

      // validar tamanho do blob da imagem
      // validar se já existe um curso com o mesmo título

      if ($this->model->store($imageData, $title, $description)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $this->data['err_image'] = false;
        $this->data['err_title'] = false;
        $this->data['err_description'] = false;
        $this->data['session_message'] = CourseMessage::ERR_FAIL_CREATE;
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }
}