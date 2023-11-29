<?php

namespace app\Controllers;

require_once __DIR__ . '/../functions/authentication.php';
require_once __DIR__ . '/../functions/validations.php';

use app\classes\GlobalValues;
use app\classes\UserMessage;
use app\Models\AdministratorModel;
use app\Models\CourseModel;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

class AdministratorController extends Controller
{
  protected ResponseFactoryInterface $responseFactory;
  protected Twig $twig;
  private array $data;
  private string $path;

  public function __construct(ResponseFactoryInterface $responseFactory, Twig $twig, string $baseURL, string $csrfToken)
  {
    $this->responseFactory = $responseFactory;
    $this->twig = $twig;

    $this->model = new AdministratorModel();

    $this->path = '/pages/administrators/';

    $this->data = [];
    $this->data['base_url'] = $baseURL;
    $this->data['csrf_token'] = $csrfToken;
  }

  public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $this->path .= 'login.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['email'] = '';
    $this->data['password'] = '';
    $this->data['err_email'] = false;
    $this->data['err_password'] = false;
    $this->data['session_message'] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data['message_type'] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();
    $email = $params['email'];
    $password = $params['password'];

    $this->path .= 'login.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['email'] = $email;
    $this->data['password'] = $password;
    $this->data['err_email'] = false;
    $this->data['err_password'] = false;
    $this->data['session_message'] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data['message_type'] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

    if ($_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID]) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::INVALID_CSRF_TOKEN;
    } elseif (!isValidEmail($email)) {
      $this->data['err_email'] = true;
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::ERR_INVALID_EMAIL;
    } elseif (!isValidPassword($password)) {
      $this->data['err_password'] = true;
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::ERR_INVALID_PASS;
    } else {
      $administratorByEmail = $this->model->getUserByEmail($email);

      if ($administratorByEmail) {
        $auth = authentication($password, $administratorByEmail->password, $this->model->getTable());

        if ($auth) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::SUCCESS_LOGIN;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
          return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
        } else {
          $this->data['err_email'] = true;
          $this->data['err_pass'] = true;
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::ERR_LOGIN;
        }
      } else {
        $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::ERR_EMAIL_NOT_FOUND;
        $this->data['err_email'] = true;
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function dashboard(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseModel = new CourseModel();
    $courses = $courseModel->all();

    $this->path .= 'dashboard.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['session_message'] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data['message_type'] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';
    $this->data['courses'] = $courses;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function logout(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $this->path .= 'login.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['email'] = '';
    $this->data['password'] = '';
    $this->data['err_email'] = false;
    $this->data['err_password'] = false;
    $this->data['session_message'] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data['message_type'] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 3600,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }

    session_destroy();

    $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::SUCCESS_LOGOUT;
    $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
    return $response->withHeader('Location', '/admin/login')->withHeader('Allow', 'GET')->withStatus(302);
  }
}