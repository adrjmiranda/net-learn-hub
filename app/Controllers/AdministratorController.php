<?php

namespace app\Controllers;

require_once __DIR__ . '/../functions/authentication.php';

use app\classes\UserMessage;
use app\Models\AdministratorModel;
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
    $this->data['session_message'] = '';

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
    $this->data['session_message'] = '';

    if (empty($email)) {
      $this->data['err_email'] = true;
      $this->data['session_message'] = UserMessage::ERR_EMPTY_EMAIL;
    } elseif (empty($password)) {
      $this->data['err_password'] = true;
      $this->data['session_message'] = UserMessage::ERR_EMPTY_PASS;
    } else {
      $administratorByEmail = $this->model->getUserByEmail($email);

      if ($administratorByEmail) {
        $auth = authentication($password, $administratorByEmail->password, $this->model->getTable());

        if ($auth) {
          return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
        } else {
          $this->data['err_email'] = true;
          $this->data['err_pass'] = true;
          $this->data['session_message'] = UserMessage::ERR_LOGIN;
        }
      } else {
        $this->data['session_message'] = UserMessage::ERR_EMAIL_NOT_FOUND;
        $this->data['err_email'] = true;
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function dashboard(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $this->path .= 'dashboard.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';

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
    $this->data['session_message'] = '';

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

    return $response->withHeader('Location', '/admin/login')->withHeader('Allow', 'GET')->withStatus(302);
  }
}