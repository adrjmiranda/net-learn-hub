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

class UserController extends Controller {
  protected ResponseFactoryInterface $responseFactory;
  protected Twig $twig;
  private array $data;
  private string $path;
  private string $googleClientId;

  public function __construct(ResponseFactoryInterface $responseFactory, Twig $twig, string $baseURL, string $csrfToken, string $googleClientId) {
    $this->responseFactory = $responseFactory;
    $this->twig = $twig;

    $this->model = new UserModel();

    $this->path = '/pages/users/';

    $this->data = [];
    $this->data['base_url'] = $baseURL;
    $this->data['csrf_token'] = $csrfToken;

    $this->googleClientId = $googleClientId;
  }

  public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
    $this->path .= 'login.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['google_client_id'] = $this->googleClientId;
    $this->data['session_message'] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data['message_type'] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function auth(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
    $params = $request->getParsedBody() ?? [];

    $credential = $params['credential'] ?? '';
    $gCsrfToken = $params['g_csrf_token'] ?? '';

    if(empty($credential) || empty($gCsrfToken)) {
    } else {
      $client = new Client(['client_id' => $_ENV['GOOGLE_CLIENT_ID']]);
      $payload = $client->verifyIdToken($credential);

      if(isset($payload['email'])) {
        // convert to object
        $payload = json_encode($payload);
        $payload = json_decode($payload);

        print_r($payload);

        $email = $payload->email;
        $firstName = $payload->given_name;
        $lastName = $payload->family_name;
        $image = $payload->picture;
      } else {
        exit;
      }
    }

    $this->path .= 'login.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['session_message'] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data['message_type'] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    return $this->twig->render($response, $this->path, $this->data);
  }
}