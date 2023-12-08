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
    $this->data['session_message'] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data['message_type'] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function auth(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody() ?? [];

    $credential = $params['credential'] ?? '';
    $googleCsrfToken = $params['g_csrf_token'] ?? '';

    $this->path .= 'login.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['session_message'] = '';
    $this->data['message_type'] = GlobalValues::TYPE_MSG_ERROR;

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

              $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::SUCCESS_LOGIN;
              $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;

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
            $_SESSION[GlobalValues::USER_ID_IDENTIFIER] = $id;

            $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = UserMessage::SUCCESS_LOGIN;
            $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;

            return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
          } else {
            $message = UserMessage::ERR_LOGIN;
          }
        }
      } else {
        $message = UserMessage::ERR_LOGIN;
      }
    }

    $this->data['session_message'] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }
}