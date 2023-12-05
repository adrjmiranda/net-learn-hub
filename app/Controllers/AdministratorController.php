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
    $this->data['session_message'] = '';
    $this->data['message_type'] = GlobalValues::TYPE_MSG_ERROR;

    if ($_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID]) {
      $message = UserMessage::INVALID_CSRF_TOKEN;
    } elseif (!isValidEmail($email)) {
      $this->data['err_email'] = true;
      $message = UserMessage::ERR_INVALID_EMAIL;
    } elseif (!isValidPassword($password)) {
      $this->data['err_password'] = true;
      $message = UserMessage::ERR_INVALID_PASS;
    } else {
      $administratorByEmail = $this->model->getUserByEmail($email);

      if ($administratorByEmail) {
        $auth = authentication($password, $administratorByEmail->password, $this->model->getTable());

        if ($auth) {
          $_SESSION[GlobalValues::ADMIN_ID_IDENTIFIER] = $administratorByEmail->id;

          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::SUCCESS_LOGIN;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
          return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
        } else {
          $this->data['err_email'] = true;
          $this->data['err_pass'] = true;
          $message = UserMessage::ERR_LOGIN;
        }
      } else {
        $message = UserMessage::ERR_EMAIL_NOT_FOUND;
        $this->data['err_email'] = true;
      }
    }

    $this->data['session_message'] = $message;

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

    return $response->withHeader('Location', '/admin/login')->withHeader('Allow', 'GET')->withStatus(302);
  }

  public function settings(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $id = (int) $_SESSION[GlobalValues::ADMIN_ID_IDENTIFIER];

    $administrator = $this->model->getById($id);

    if (empty($administrator)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $this->path .= 'admin_settings.html.twig';
      $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
      $this->data['admin_id'] = $administrator->id;
      $this->data['image'] = $administrator->image;
      $this->data['first_name'] = $administrator->first_name;
      $this->data['last_name'] = $administrator->last_name;
      $this->data['email'] = $administrator->email;
      $this->data['password'] = '';
      $this->data['password_confirmation'] = '';
      $this->data['description'] = $administrator->description;
      $this->data['err_first_name'] = false;
      $this->data['err_last_name'] = false;
      $this->data['err_email'] = false;
      $this->data['err_password'] = false;
      $this->data['err_password_confirmation'] = false;
      $this->data['err_description'] = false;
      $this->data['session_message'] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
      $this->data['message_type'] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processUpdateRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();

    $adminIdIdentifier = (int) $_SESSION[GlobalValues::ADMIN_ID_IDENTIFIER];
    $adminId = (int) ($params['admin_id'] ?? '');
    $image = $params['image'];
    $firstName = $params['first_name'];
    $lastName = $params['last_name'];
    $password = $params['password'];
    $passwordConfirmation = $params['password_confirmation'];
    $description = $params['description'];

    $imageDirectory = __DIR__ . '/../../public/images';
    $imageFiles = [];

    if (is_dir($imageDirectory)) {
      $files = scandir($imageDirectory);

      $files = array_diff($files, array('.', '..'));

      foreach ($files as $file) {
        $imageFiles[] = $file;
      }
    }

    if (!isValidId($adminId)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::ERR_EMAIL_NOT_FOUND;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } elseif ($adminIdIdentifier !== $adminId) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::ERR_EMAIL_NOT_FOUND;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $administrator = $this->model->getById($adminId);

      if (empty($administrator)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $this->path .= 'admin_settings.html.twig';
        $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
        $this->data['admin_id'] = $adminId;
        $this->data['image'] = $image;
        $this->data['first_name'] = $firstName;
        $this->data['last_name'] = $lastName;
        $this->data['email'] = $administrator->email;
        $this->data['password'] = $password;
        $this->data['password_confirmation'] = $passwordConfirmation;
        $this->data['description'] = $description;
        $this->data['err_first_name'] = false;
        $this->data['err_last_name'] = false;
        $this->data['err_email'] = false;
        $this->data['err_password'] = false;
        $this->data['err_password_confirmation'] = false;
        $this->data['err_description'] = false;
        $this->data['session_message'] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
        $this->data['message_type'] = GlobalValues::TYPE_MSG_ERROR;

        if (!in_array($image, $imageFiles)) {
          $message = UserMessage::ERR_INVALID_IMAGE_TYPE;
        } elseif (!isValidText($firstName, 'first_name')) {
          $this->data['err_first_name'] = true;
          $message = UserMessage::ERR_INVALID_FIRST_NAME;
        } elseif (!isValidText($lastName, 'last_name')) {
          $this->data['err_last_name'] = true;
          $message = UserMessage::ERR_INVALID_LAST_NAME;
        } elseif (!isValidText($description, 'description')) {
          $this->data['err_description'] = true;
          $message = UserMessage::ERR_INVALID_DESCRIPTION;
        } else {
          $invalidPassword = false;

          if (!empty($password)) {
            $invalidPassword = true;

            if (!isValidPassword($password)) {
              $this->data['err_password'] = true;
              $message = UserMessage::ERR_INVALID_PASS;
            } elseif (!isValidPassword($passwordConfirmation)) {
              $this->data['err_password_confirmation'] = true;
              $message = UserMessage::ERR_INVALID_PASS;
            } elseif (!($password === $passwordConfirmation)) {
              $this->data['err_password'] = true;
              $this->data['err_password_confirmation'] = true;
              $message = UserMessage::ERR_INVALID_PASS_CONFIRMATION;
            } else {
              $invalidPassword = false;
            }
          }

          if (!$invalidPassword) {
            if (!empty($password)) {
              $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            } else {
              $passwordHash = $administrator->password;
            }

            if ($this->model->update($adminId, $firstName, $lastName, $passwordHash, $image, $description)) {
              $message = UserMessage::SUCCESS_UPDATE;
              $this->data['message_type'] = GlobalValues::TYPE_MSG_SUCCESS;
            } else {
              $message = UserMessage::ERR_FAIL_UPDATE;
            }
          }
        }
      }
    }

    $this->data['session_message'] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }
}