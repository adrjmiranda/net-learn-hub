<?php

namespace app\Controllers;

use app\classes\GlobalValues;
use app\classes\UserMessage;
use app\Models\CommentModel;
use app\Models\CourseModel;
use app\Models\UserModel;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;
use Google\Client;

class UserController extends Controller
{
  protected ResponseFactoryInterface $responseFactory;
  protected Twig $twig;
  private array $data;
  private string $path;
  private string $googleClientId;

  public function __construct(ResponseFactoryInterface $responseFactory, Twig $twig, string $baseURL, string $gCsrfToken, string $googleClientId)
  {
    $this->responseFactory = $responseFactory;
    $this->twig = $twig;

    $this->model = new UserModel();

    $this->path = '/pages/users/';

    $this->data = [];
    $this->data['base_url'] = $baseURL;
    $this->data[GlobalValues::G_CSRF_TOKEN] = $gCsrfToken;

    $this->googleClientId = $googleClientId;
  }

  public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $this->path .= 'login.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['google_client_id'] = $this->googleClientId;
    $this->data[GlobalValues::SESSION_MESSAGE] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function auth(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody() ?? [];

    $credential = $params['credential'] ?? '';
    $googleCsrfToken = $params['g_csrf_token'] ?? '';

    $this->path .= 'login.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data[GlobalValues::USER_IS_CONNECTED] = $_SESSION[GlobalValues::USER_IS_CONNECTED];
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

    if (empty($credential) || empty($googleCsrfToken)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::ERR_LOGIN;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $client = new Client(['client_id' => $_ENV['GOOGLE_CLIENT_ID']]);
      $payload = $client->verifyIdToken($credential);

      if (isset($payload['email'])) {
        // convert to object
        $payload = json_encode($payload);
        $payload = json_decode($payload);

        $email = $payload->email;
        $firstName = $payload->given_name ?? '';
        $lastName = $payload->family_name ?? '';
        $image = $payload->picture ?? '';

        $userByEmail = $this->model->getUserByEmail($email);

        if (empty($userByEmail)) {
          if ($this->model->store($email, $firstName, $lastName, $image)) {
            $userCreatedByEmail = $this->model->getUserByEmail($email);

            if (!empty($userCreatedByEmail)) {
              $_SESSION[GlobalValues::USER_TOKEN] ??= $credential;
              $_SESSION[GlobalValues::USER_ID_IDENTIFIER] ??= $userCreatedByEmail->id;

              $_SESSION[GlobalValues::USER_IS_CONNECTED] = true;

              return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
            } else {
              $message = UserMessage::ERR_LOGIN;
            }
          } else {
            $message = UserMessage::ERR_LOGIN;
          }
        } else {
          $id = $userByEmail->id;

          if ($this->model->update($id, $firstName, $lastName, $image)) {
            $_SESSION[GlobalValues::USER_TOKEN] ??= $credential;
            $_SESSION[GlobalValues::USER_ID_IDENTIFIER] ??= $id;

            $_SESSION[GlobalValues::USER_IS_CONNECTED] = true;

            return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
          } else {
            $message = UserMessage::ERR_LOGIN;
          }
        }
      } else {
        $message = UserMessage::ERR_LOGIN;
      }
    }

    $this->data[GlobalValues::SESSION_MESSAGE] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function logout(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
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

    return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
  }

  public function processCommentRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $this->path = '/pages/courses/';
    $courseModel = new CourseModel();

    $courses = $courseModel->getActiveVisibility() ?? [];

    $params = $request->getParsedBody();
    $userId = (int) ($params['user_id'] ?? '');
    $comment = $params['comment'];

    $userIdIdentifier = $_SESSION[GlobalValues::USER_ID_IDENTIFIER];

    $commentModel = new CommentModel();

    $users = $this->model->all() ?? [];

    foreach ($users as $user) {
      if (property_exists($user, 'image')) {
        $user->image = base64_decode($user->image);
      }
    }

    $this->path .= 'home.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data[GlobalValues::USER_IS_CONNECTED] = $_SESSION[GlobalValues::USER_IS_CONNECTED];
    $this->data['courses'] = $courses;
    $this->data['users'] = $users;
    $this->data['user_id'] = $userId;
    $this->data['comment'] = $comment;
    $this->data['err_comment'] = false;
    $this->data['user_already_commented'] = false;
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

    $commentModel = new CommentModel();

    $userById = $this->model->getById($userId);

    if ($_SESSION[GlobalValues::G_CSRF_TOKEN_IS_INVALID]) {
      $message = UserMessage::INVALID_CSRF_TOKEN;
    } elseif (!isValidId($userId) || empty($userById)) {
      echo 'here';
      return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
    } elseif ($userIdIdentifier !== $userId) {
      return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
    } elseif (!isValidText($comment, 'comment')) {

      $this->data['err_comment'] = true;
      $message = UserMessage::ERR_INVALID_COMMENT;
    } else {
      $this->data['err_comment'] = false;

      if ($commentModel->store($comment, $userId)) {
        $message = UserMessage::ERR_INVALID_COMMENT;
        $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
      } else {
        $message = UserMessage::ERR_FAILED_TO_SAVE_COMMENT;
      }
    }

    $commentByUserId = $commentModel->getByUserId($userId) ?? [];
    if (count($commentByUserId) > 0) {
      $this->data['user_already_commented'] = true;
    }

    $comments = $commentModel->all() ?? [];

    $this->data['comments'] = $comments;

    $this->data[GlobalValues::SESSION_MESSAGE] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }
}