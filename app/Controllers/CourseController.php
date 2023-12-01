<?php

namespace app\Controllers;

use app\classes\CourseMessage;
use app\classes\GlobalValues;
use app\classes\UserMessage;
use app\Models\CourseModel;
use app\Models\QuizModel;
use app\Models\TopicModel;
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
        $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_CREATE_COURSE;
        $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $message = CourseMessage::ERR_FAIL_CREATE_COURSE;
      }
    }

    $this->data['session_message'] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $id = (int) ($args['id'] ?? '');

    if (!isValidId($id)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $courseById = $this->model->getById($id);

      if (empty($courseById)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $this->path .= 'edit_course.html.twig';
        $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
        $this->data['id'] = $courseById->id;
        $this->data['title'] = $courseById->title;
        $this->data['workload'] = $courseById->workload;
        $this->data['description'] = $courseById->description;
        $this->data['err_image'] = false;
        $this->data['err_title'] = false;
        $this->data['err_workload'] = false;
        $this->data['err_description'] = false;
        $this->data['session_message'] = '';
        $this->data['message_type'] = '';
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processUpdateRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();

    $uploadedImage = $_FILES['image'] ?? [];
    $id = (int) ($params['id'] ?? '');
    $title = $params['title'];
    $workload = $params['workload'];
    $description = $params['description'];

    $imagesTypes = ['image/jpeg', 'image/jpg', 'image/png'];

    $imageType = $uploadedImage['type'];

    $this->path .= 'edit_course.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['id'] = $id;
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
    } else {
      $courseById = $this->model->getById($id);

      if (empty($courseById)) {
        $message = CourseMessage::ERR_COURSE_INEXISTENT;
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

        $image = null;

        if ($uploadedImage['size'] !== 0 || $uploadedImage['error'] !== UPLOAD_ERR_NO_FILE) {
          if ($uploadedImage['error'] !== UPLOAD_ERR_OK || !in_array($imageType, $imagesTypes)) {
            $this->data['err_image'] = true;
            $message = CourseMessage::ERR_INVALID_IMAGE_TYPE;
          } elseif (!isValidBlob(file_get_contents($uploadedImage['tmp_name']), 'image')) {
            $this->data['err_image'] = true;
            $message = CourseMessage::ERR_INVALID_IMAGE_LENGTH;
          } else {
            $image = file_get_contents($uploadedImage['tmp_name']);
          }
        }

        if ($this->data['err_image'] === false) {
          if ($this->model->update($id, $image, $title, $workload, $description)) {
            $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_UPDATE_COURSE;
            $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
            return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
          } else {
            $message = CourseMessage::ERR_FAIL_UPDATE_COURSE;
          }
        }
      }
    }

    $this->data['session_message'] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processDeleteRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $id = (int) ($args['id'] || '');

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function topics(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');

    $this->path .= 'course_topics.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['course_name'] = '';
    $this->data['topics'] = [];
    $this->data['session_message'] = '';
    $this->data['message_type'] = GlobalValues::TYPE_MSG_ERROR;

    if (!isValidId($courseId)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $topicModel = new TopicModel();

      $topicsByCourseId = $topicModel->getByCourseId($courseId);
      $course = $this->model->getById($courseId);

      $this->data['course_name'] = $course->title;
      $this->data['topics'] = $topicsByCourseId;
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function createTopic(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');

    if (!isValidId($courseId)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $courseById = $this->model->getById($courseId);

      if (empty($courseById)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $this->path .= 'create_topic.html.twig';
        $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
        $this->data['course_id'] = $courseById->id;
        $this->data['course_name'] = $courseById->title;
        $this->data['title'] = '';
        $this->data['content'] = null;
        $this->data['err_title'] = false;
        $this->data['err_content'] = false;
        $this->data['session_message'] = '';
        $this->data['message_type'] = '';
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processStoreTopicRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();

    $courseId = (int) ($params['course_id'] ?? '');
    $title = $params['title'];
    $content = $params['content'];

    $this->path .= 'create_topic.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['title'] = $title;
    $this->data['content'] = $content;
    $this->data['err_title'] = false;
    $this->data['err_content'] = false;
    $this->data['session_message'] = '';
    $this->data['message_type'] = GlobalValues::TYPE_MSG_ERROR;

    if ($_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID]) {
      $message = UserMessage::INVALID_CSRF_TOKEN;
    } elseif (!isValidText($title, 'title')) {
      $this->data['err_title'] = true;
      $message = CourseMessage::ERR_INVALID_TITLE;
    } elseif (!isValidBlob($content, 'document')) {
      $this->data['err_content'] = true;
      $message = CourseMessage::ERR_INVALID_TOPIC_CONTENT;
    } else {
      $this->data['err_title'] = false;
      $this->data['err_content'] = false;

      $courseById = $this->model->getById($courseId);

      if (empty($courseById)) {
        $message = CourseMessage::ERR_COURSE_INEXISTENT;
      } else {
        $topicModel = new TopicModel();
        $topicByTitleAndCourseId = $topicModel->getByTitleByCourseId($title, $courseId);


        if ($topicByTitleAndCourseId) {
          $this->data['err_title'] = true;
          $message = CourseMessage::ERR_TITLE_ALREADY_EXISTS;
        } elseif ($topicModel->store($title, $content, $courseId)) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_CREATE_TOPIC;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
          return $response->withHeader('Location', '/admin/course/topics/' . $courseId)->withHeader('Allow', 'GET')->withStatus(302);
        } else {
          $message = CourseMessage::ERR_FAIL_CREATE_TOPIC;
        }
      }

    }

    $this->data['session_message'] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function quizzes(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');

    $this->path .= 'course_quizzes.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['course_name'] = '';
    $this->data['session_message'] = '';
    $this->data['message_type'] = GlobalValues::TYPE_MSG_ERROR;

    if (!isValidId($courseId)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $quizModel = new QuizModel();

      $quizzesByCourseId = $quizModel->getByCourseId($courseId);
      $course = $this->model->getById($courseId);

      $this->data['course_name'] = $course->title;
      $this->data['quizzes'] = $quizzesByCourseId;
    }

    return $this->twig->render($response, $this->path, $this->data);
  }
}