<?php

namespace app\Controllers;

use app\classes\CourseMessage;
use app\classes\GlobalValues;
use app\classes\UserMessage;
use app\Models\AlternativeModel;
use app\Models\CommentModel;
use app\Models\CourseModel;
use app\Models\QuestionModel;
use app\Models\QuizModel;
use app\Models\TopicModel;
use app\Models\UserCourseRelationModel;
use app\Models\UserQuizRelationModel;
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

  public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courses = $this->model->getActiveVisibility() ?? [];

    $this->path .= 'home.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data[GlobalValues::USER_IS_CONNECTED] = $_SESSION[GlobalValues::USER_IS_CONNECTED];
    $this->data['courses'] = $courses;
    $this->data[GlobalValues::SESSION_MESSAGE] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    return $this->twig->render($response, $this->path, $this->data);
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
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = '';

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
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

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

    $this->data[GlobalValues::SESSION_MESSAGE] = $message;

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
        $this->data[GlobalValues::SESSION_MESSAGE] = '';
        $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = '';
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
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

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

    $this->data[GlobalValues::SESSION_MESSAGE] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processDeleteRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');

    $courseById = $this->model->getById($courseId);

    if (empty($courseById)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_COURSE_INEXISTENT;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $alternativeModel = new AlternativeModel();
      $questionModel = new QuestionModel();
      $userQuizRelationModel = new UserQuizRelationModel();
      $quizModel = new QuizModel();
      $commentModel = new CommentModel();
      $topicModel = new TopicModel();
      $userCourseRelationModel = new UserCourseRelationModel();

      if (
        $alternativeModel->delete($courseId, 'course_id') &&
        $questionModel->delete($courseId, 'course_id') &&
        $userQuizRelationModel->delete($courseId, 'course_id') &&
        $quizModel->delete($courseId, 'course_id') &&
        $commentModel->delete($courseId, 'course_id') &&
        $topicModel->delete($courseId, 'course_id') &&
        $userCourseRelationModel->delete($courseId, 'course_id')
      ) {
        if ($this->model->delete($courseId, 'id')) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_DELETE_COURSE;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
        } else {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_FAIL_DELETE_COURSE;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
        }
      } else {
        $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_FAIL_DELETE_COURSE;
        $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      }
    }

    return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
  }

  public function topics(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');

    $this->path .= 'course_topics.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['course_name'] = '';
    $this->data['topics'] = [];
    $this->data[GlobalValues::SESSION_MESSAGE] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    if (!isValidId($courseId)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $courseById = $this->model->getById($courseId);

      if (empty($courseById)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $topicModel = new TopicModel();

        $topicsByCourseId = $topicModel->getByCourseId($courseId);
        $course = $this->model->getById($courseId);

        $this->data['course_name'] = $course->title;
        $this->data['topics'] = $topicsByCourseId;
      }
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
        $this->data[GlobalValues::SESSION_MESSAGE] = '';
        $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = '';
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processTopicStoreRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
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
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

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
        $topicByTitleAndCourseId = $topicModel->getByTitleAndCourseId($title, $courseId);

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

    $this->data[GlobalValues::SESSION_MESSAGE] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function editTopic(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {

    $courseId = (int) ($args['course_id'] ?? '');
    $topicId = (int) ($args['topic_id'] ?? '');

    if (!isValidId($courseId) || !isValidId($topicId)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $courseById = $this->model->getById($courseId);
      $topicModel = new TopicModel();

      $topicByIdAndCourseId = $topicModel->getByIdAndCourseId($topicId, $courseId);

      if (empty($courseById) || empty($topicByIdAndCourseId)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $title = $topicByIdAndCourseId->title;
        $content = $topicByIdAndCourseId->content;

        $this->path .= 'edit_topic.html.twig';
        $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
        $this->data['course_id'] = $courseById->id;
        $this->data['topic_id'] = $topicByIdAndCourseId->id;
        $this->data['course_name'] = $courseById->title;
        $this->data['title'] = $title;
        $this->data['content'] = $content;
        $this->data['err_title'] = false;
        $this->data['err_content'] = false;
        $this->data[GlobalValues::SESSION_MESSAGE] = '';
        $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = '';
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processTopicUpdateRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();

    $courseId = (int) ($params['course_id'] ?? '');
    $topicId = (int) ($params['topic_id'] ?? '');
    $title = $params['title'];
    $content = $params['content'];

    $this->path .= 'edit_topic.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['topic_id'] = $topicId;
    $this->data['title'] = $title;
    $this->data['content'] = $content;
    $this->data['err_title'] = false;
    $this->data['err_content'] = false;
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

    if ($_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID]) {
      $message = UserMessage::INVALID_CSRF_TOKEN;
    } else {
      $courseById = $this->model->getById($courseId);
      $topicModel = new TopicModel();

      $topicByIdAndCourseId = $topicModel->getByIdAndCourseId($topicId, $courseId);
      $topicByTitleAndCourseId = $topicModel->getByTitleAndCourseId($title, $courseId);

      if (empty($courseById) || empty($topicByIdAndCourseId)) {
        $message = CourseMessage::ERR_TOPIC_INEXISTENT;
      } elseif (!isValidText($title, 'title')) {
        $this->data['course_name'] = $courseById->title;

        $this->data['err_title'] = true;
        $message = CourseMessage::ERR_INVALID_TITLE;
      } elseif ($topicByTitleAndCourseId) {
        $this->data['err_title'] = true;
        $message = CourseMessage::ERR_TITLE_ALREADY_EXISTS;
      } elseif (!isValidBlob($content, 'document')) {
        $this->data['err_content'] = true;
        $message = CourseMessage::ERR_INVALID_TOPIC_CONTENT;
      } else {
        $this->data['err_title'] = false;
        $this->data['err_content'] = false;

        if ($topicModel->update($topicId, $title, $content)) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_UPDATE_TOPIC;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
          return $response->withHeader('Location', '/admin/course/topics/' . $courseId)->withHeader('Allow', 'GET')->withStatus(302);
        } else {
          $message = CourseMessage::ERR_FAIL_UPDATE_TOPIC;
        }
      }
    }

    $this->data[GlobalValues::SESSION_MESSAGE] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processTopicDeleteRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');
    $topicId = (int) ($args['topic_id'] ?? '');

    if (!isValidId($courseId) || !isValidId($topicId)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_COURSE_INEXISTENT;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $courseById = $this->model->getById($courseId);
      $topicModel = new TopicModel();

      $topicByIdAndCourseId = $topicModel->getByIdAndCourseId($topicId, $courseId);

      if (empty($courseById) || empty($topicByIdAndCourseId)) {
        $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_TOPIC_INEXISTENT;
        $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        if ($topicModel->delete($topicId, 'id')) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_DELETE_TOPIC;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
        } else {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_FAIL_DELETE_TOPIC;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
        }
      }
    }

    return $response->withHeader('Location', '/admin/course/topics/' . $courseId)->withHeader('Allow', 'GET')->withStatus(302);
  }

  public function quizzes(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');

    $this->path .= 'course_quizzes.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['course_name'] = '';
    $this->data['quizzes'] = [];
    $this->data[GlobalValues::SESSION_MESSAGE] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    if (!isValidId($courseId)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $courseById = $this->model->getById($courseId);

      if (empty($courseById)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $quizModel = new QuizModel();

        $quizzesByCourseId = $quizModel->getByCourseId($courseId);
        $course = $this->model->getById($courseId);

        $this->data['course_name'] = $course->title;
        $this->data['quizzes'] = $quizzesByCourseId;
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function createQuiz(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');

    if (!isValidId($courseId)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $courseById = $this->model->getById($courseId);

      if (empty($courseById)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $this->path .= 'create_quiz.html.twig';
        $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
        $this->data['course_id'] = $courseById->id;
        $this->data['course_name'] = $courseById->title;
        $this->data['title'] = '';
        $this->data['err_title'] = false;
        $this->data[GlobalValues::SESSION_MESSAGE] = '';
        $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = '';
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processQuizStoreRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();

    $courseId = (int) ($params['course_id'] ?? '');
    $title = $params['title'];

    $this->path .= 'create_quiz.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['title'] = $title;
    $this->data['err_title'] = false;
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

    if ($_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID]) {
      $message = UserMessage::INVALID_CSRF_TOKEN;
    } elseif (!isValidText($title, 'title')) {
      $this->data['err_title'] = true;
      $message = CourseMessage::ERR_INVALID_TITLE;
    } else {
      $this->data['err_title'] = false;

      $courseById = $this->model->getById($courseId);

      if (empty($courseById)) {
        $message = CourseMessage::ERR_COURSE_INEXISTENT;
      } else {
        $quizModel = new QuizModel();
        $quizByTitleAndCourseId = $quizModel->getByTitleAndCourseId($title, $courseId);

        if ($quizByTitleAndCourseId) {
          $this->data['err_title'] = true;
          $message = CourseMessage::ERR_TITLE_ALREADY_EXISTS;
        } elseif ($quizModel->store($title, $courseId)) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_CREATE_QUIZ;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
          return $response->withHeader('Location', '/admin/course/quizzes/' . $courseId)->withHeader('Allow', 'GET')->withStatus(302);
        } else {
          $message = CourseMessage::ERR_FAIL_CREATE_QUIZ;
        }
      }
    }

    $this->data[GlobalValues::SESSION_MESSAGE] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function editQuiz(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {

    $courseId = (int) ($args['course_id'] ?? '');
    $quizId = (int) ($args['quiz_id'] ?? '');

    if (!isValidId($courseId) || !isValidId($quizId)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $courseById = $this->model->getById($courseId);
      $quizModel = new QuizModel();

      $quizByTitleAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);

      if (empty($courseById) || empty($quizByTitleAndCourseId)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $title = $quizByTitleAndCourseId->title;

        $this->path .= 'edit_quiz.html.twig';
        $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
        $this->data['course_id'] = $courseById->id;
        $this->data['quiz_id'] = $quizByTitleAndCourseId->id;
        $this->data['course_name'] = $courseById->title;
        $this->data['title'] = $title;
        $this->data['err_title'] = false;
        $this->data[GlobalValues::SESSION_MESSAGE] = '';
        $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = '';
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processQuizUpdateRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();

    $courseId = (int) ($params['course_id'] ?? '');
    $quizId = (int) ($params['quiz_id'] ?? '');
    $title = $params['title'];

    $this->path .= 'edit_quiz.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['quiz_id'] = $quizId;
    $this->data['title'] = $title;
    $this->data['err_title'] = false;
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

    if ($_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID]) {
      $message = UserMessage::INVALID_CSRF_TOKEN;
    } else {
      $courseById = $this->model->getById($courseId);
      $quizModel = new QuizModel();

      $quizByIdAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);
      $quizByTitleAndCourseId = $quizModel->getByTitleAndCourseId($title, $courseId);

      if (empty($courseById) || empty($quizByIdAndCourseId)) {
        $message = CourseMessage::ERR_QUIZ_INEXISTENT;
      } elseif (!isValidText($title, 'title')) {
        $this->data['course_name'] = $courseById->title;

        $this->data['err_title'] = true;
        $message = CourseMessage::ERR_INVALID_TITLE;
      } elseif ($quizByTitleAndCourseId) {
        $this->data['err_title'] = true;
        $message = CourseMessage::ERR_TITLE_ALREADY_EXISTS;
      } else {
        $this->data['err_title'] = false;
        $this->data['err_content'] = false;

        if ($quizModel->update($quizId, $title)) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_UPDATE_QUIZ;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
          return $response->withHeader('Location', '/admin/course/quizzes/' . $courseId)->withHeader('Allow', 'GET')->withStatus(302);
        } else {
          $message = CourseMessage::ERR_FAIL_UPDATE_QUIZ;
        }
      }
    }

    $this->data[GlobalValues::SESSION_MESSAGE] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processQuizDeleteRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');
    $quizId = (int) ($args['quiz_id'] ?? '');

    if (!isValidId($courseId) || !isValidId($quizId)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_QUIZ_INEXISTENT;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $quizModel = new QuizModel();

      $courseById = $this->model->getById($courseId);
      $quizByIdAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);

      if (empty($courseById) || empty($quizByIdAndCourseId)) {
        $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_QUIZ_INEXISTENT;
        $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $questionModel = new QuestionModel();
        $userQuizRelationModel = new UserQuizRelationModel();

        if (
          $questionModel->delete($courseId, 'course_id') &&
          $userQuizRelationModel->delete($courseId, 'course_id')
        ) {
          if ($quizModel->delete($quizId, 'id')) {
            $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_DELETE_QUIZ;
            $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
          } else {
            $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_FAIL_DELETE_QUIZ;
            $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
          }
        } else {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_FAIL_DELETE_QUIZ;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
        }
      }
    }

    return $response->withHeader('Location', '/admin/course/quizzes/' . $courseId)->withHeader('Allow', 'GET')->withStatus(302);
  }

  public function processVisibilityRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');

    $courseById = $this->model->getById($courseId);


    if (!isValidId($courseId)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_COURSE_INEXISTENT;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } elseif (empty($courseById)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_COURSE_INEXISTENT;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $topicModel = new TopicModel();

      $allTopicByCourseId = $topicModel->getByCourseId($courseId);

      $courseVisibility = (int) $courseById->visibility;

      if (empty($allTopicByCourseId)) {
        $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_COURSE_NOT_POSSIBLE_TOPIC;
        $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      } else {
        $newVisibility = $courseVisibility == 0 ? 1 : 0;

        if ($this->model->setVisibility($courseId, $newVisibility)) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_TO_CHANGE_COURSE_VISIBILITY;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
        } else {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_FAIL_TO_CHANGE_COURSE_VISIBILITY;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
        }
      }
    }

    return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
  }

  public function questions(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');
    $quizId = (int) ($args['quiz_id'] ?? '');

    $this->path .= 'course_quiz_questions.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['quiz_id'] = $quizId;
    $this->data['quiz_name'] = '';
    $this->data['questions'] = [];
    $this->data[GlobalValues::SESSION_MESSAGE] = $_SESSION[GlobalValues::SESSION_MESSAGE] ?? '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] ?? '';

    if (!isValidId($courseId) || !isValidId($quizId)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $quizModel = new QuizModel();

      $courseById = $this->model->getById($courseId);
      $quizByIdAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);

      if (empty($courseById) || empty($quizByIdAndCourseId)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $questionModel = new QuestionModel();

        $questionByQuizId = $questionModel->getByQuizId($quizId);

        $this->data['quiz_name'] = $quizByIdAndCourseId->title;
        $this->data['questions'] = $questionByQuizId;
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function createQuestion(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');
    $quizId = (int) ($args['quiz_id'] ?? '');

    if (!isValidId($courseId) || !isValidId($quizId)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_QUIZ_INEXISTENT;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $quizModel = new QuizModel();
      $questionModel = new QuestionModel();

      $courseById = $this->model->getById($courseId);
      $quizByIdAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);
      $questionsByQuizId = $questionModel->getByQuizId($quizId) ?? [];

      $questionsNumber = count($questionsByQuizId);

      if (empty($courseById) || empty($quizByIdAndCourseId)) {
        $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_QUIZ_INEXISTENT;
        $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        if ($questionsNumber == GlobalValues::MAXIMUM_QUANTITY_QUESTIONS) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_MAXIMUM_QUESTIONS_FOR_ONE_QUIZ;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
          return $response->withHeader('Location', '/admin/course/quizzes/questions/' . $courseId . '/' . $quizId)->withHeader('Allow', 'GET')->withStatus(302);
        } elseif ($questionsNumber < GlobalValues::MAXIMUM_QUANTITY_QUESTIONS) {
          $this->path .= 'create_quiz_question.html.twig';
          $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
          $this->data['course_id'] = $courseById->id;
          $this->data['quiz_id'] = $quizByIdAndCourseId->id;
          $this->data['quiz_name'] = $quizByIdAndCourseId->title;
          $this->data['question'] = '';
          $this->data['correct'] = '';
          $this->data['alternative_1'] = '';
          $this->data['alternative_2'] = '';
          $this->data['alternative_3'] = '';
          $this->data['alternative_4'] = '';
          $this->data['alternative_5'] = '';
          $this->data['err_question'] = false;
          $this->data['err_correct'] = false;
          $this->data['err_alternative_1'] = false;
          $this->data['err_alternative_2'] = false;
          $this->data['err_alternative_3'] = false;
          $this->data['err_alternative_4'] = false;
          $this->data['err_alternative_5'] = false;
          $this->data[GlobalValues::SESSION_MESSAGE] = '';
          $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = '';
        }
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processQuestionStoreRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();

    $courseId = (int) ($params['course_id'] ?? '');
    $quizId = (int) ($params['quiz_id'] ?? '');
    $question = $params['question'];
    $correct = (int) ($params['correct'] ?? '');
    $alternative1 = $params['alternative_1'];
    $alternative2 = $params['alternative_2'];
    $alternative3 = $params['alternative_3'];
    $alternative4 = $params['alternative_4'];
    $alternative5 = $params['alternative_5'];

    $this->path .= 'create_quiz_question.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['quiz_id'] = $quizId;
    $this->data['question'] = $question;
    $this->data['correct'] = $correct;
    $this->data['alternative_1'] = $alternative1;
    $this->data['alternative_2'] = $alternative2;
    $this->data['alternative_3'] = $alternative3;
    $this->data['alternative_4'] = $alternative4;
    $this->data['alternative_5'] = $alternative5;
    $this->data['err_question'] = false;
    $this->data['err_correct'] = false;
    $this->data['err_alternative_1'] = false;
    $this->data['err_alternative_2'] = false;
    $this->data['err_alternative_3'] = false;
    $this->data['err_alternative_4'] = false;
    $this->data['err_alternative_5'] = false;
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

    $quizModel = new QuizModel();

    $courseById = $this->model->getById($courseId);
    $quizByIdAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);

    if ($_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID]) {
      $message = UserMessage::INVALID_CSRF_TOKEN;
    } elseif (empty($courseById) || empty($quizByIdAndCourseId)) {
      $message = CourseMessage::ERR_QUIZ_INEXISTENT;
    } elseif (!isValidText($question, 'question')) {
      $this->data['err_question'] = true;
      $message = CourseMessage::ERR_INVALID_QUESTION_TEXT;
    } elseif (!isValidQuestionNumber($correct)) {
      $this->data['err_correct'] = true;
      $message = CourseMessage::ERR_INVALID_QUESTION_NUMBER;
    } elseif (!isValidText($alternative1, 'alternative')) {
      $this->data['err_alternative_1'] = true;
      $message = CourseMessage::ERR_INVALID_ALTERNATIVE;
    } elseif (!isValidText($alternative2, 'alternative')) {
      $this->data['err_alternative_2'] = true;
      $message = CourseMessage::ERR_INVALID_ALTERNATIVE;
    } elseif (!isValidText($alternative3, 'alternative')) {
      $this->data['err_alternative_3'] = true;
      $message = CourseMessage::ERR_INVALID_ALTERNATIVE;
    } elseif (!isValidText($alternative4, 'alternative')) {
      $this->data['err_alternative_4'] = true;
      $message = CourseMessage::ERR_INVALID_ALTERNATIVE;
    } elseif (!isValidText($alternative5, 'alternative')) {
      $this->data['err_alternative_5'] = true;
      $message = CourseMessage::ERR_INVALID_ALTERNATIVE;
    } else {
      $this->data['err_question'] = false;
      $this->data['err_correct'] = false;
      $this->data['err_alternative_1'] = false;
      $this->data['err_alternative_2'] = false;
      $this->data['err_alternative_3'] = false;
      $this->data['err_alternative_4'] = false;
      $this->data['err_alternative_5'] = false;

      $alternatives = [];

      for ($i = 1; $i <= 5; $i++) {
        $variableName = 'alternative' . $i;

        if (isset($$variableName)) {
          $alternatives[] = [$i, $$variableName];
        }
      }

      $questionModel = new QuestionModel();
      $alternativeModel = new AlternativeModel();

      $questionsByQuizId = $questionModel->getByQuizId($quizId) ?? [];
      $questionsNumber = count($questionsByQuizId);

      if ($questionsNumber == GlobalValues::MAXIMUM_QUANTITY_QUESTIONS) {
        $message = CourseMessage::ERR_MAXIMUM_QUESTIONS_FOR_ONE_QUIZ;
      } elseif ($questionsNumber < GlobalValues::MAXIMUM_QUANTITY_QUESTIONS) {
        $newQuestion = $questionModel->store($question, $correct, $courseId, $quizId);

        if (!empty($newQuestion)) {
          $errorSavingAlternative = false;

          foreach ($alternatives as $alternative) {
            if (!$alternativeModel->store($alternative[1], $alternative[0], $courseId, $newQuestion->id)) {
              $errorSavingAlternative = true;
            }
          }

          if ($errorSavingAlternative) {
            $message = CourseMessage::ERR_WHEN_SAVING_ONE_OF_ALTERNATIVES;
          } else {
            $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_CREATE_QUESTION;
            $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
            return $response->withHeader('Location', '/admin/course/quizzes/questions/' . $courseId . '/' . $quizId)->withHeader('Allow', 'GET')->withStatus(302);
          }
        } else {
          $message = CourseMessage::ERR_FAIL_CREATE_QUESTION;
        }
      }
    }

    $this->data[GlobalValues::SESSION_MESSAGE] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function editQuestion(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');
    $quizId = (int) ($args['quiz_id'] ?? '');
    $questionId = (int) ($args['question_id'] ?? '');

    if (!isValidId($courseId) || !isValidId($quizId) || !isValidId($questionId)) {
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $quizModel = new QuizModel();
      $questionModel = new QuestionModel();
      $alternativeModel = new AlternativeModel();

      $courseById = $this->model->getById($courseId);
      $quizByIdAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);
      $questionByIdAndCourseId = $questionModel->getByIdAndCourseId($questionId, $courseId);

      if (empty($courseById) || empty($quizByIdAndCourseId) || empty($questionByIdAndCourseId)) {
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $this->path .= 'edit_quiz_question.html.twig';
        $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
        $this->data['course_id'] = $courseById->id;
        $this->data['quiz_id'] = $quizByIdAndCourseId->id;
        $this->data['question_id'] = $questionByIdAndCourseId->id;
        $this->data['quiz_name'] = $quizByIdAndCourseId->title;
        $this->data['question'] = $questionByIdAndCourseId->question;
        $this->data['correct'] = $questionByIdAndCourseId->correct;
        $this->data['err_question'] = false;
        $this->data['err_correct'] = false;
        $this->data['err_alternative_1'] = false;
        $this->data['err_alternative_2'] = false;
        $this->data['err_alternative_3'] = false;
        $this->data['err_alternative_4'] = false;
        $this->data['err_alternative_5'] = false;
        $this->data[GlobalValues::SESSION_MESSAGE] = '';
        $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = '';

        $alternativeModel = new AlternativeModel();

        $alternativesByQuestionId = $alternativeModel->getByQuestionId($questionId);

        foreach ($alternativesByQuestionId as $index => $alternative) {
          $this->data['alternative_' . ($index + 1)] = $alternative->alternative;
        }
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processQuestionUpdateRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $params = $request->getParsedBody();

    $courseId = (int) ($params['course_id'] ?? '');
    $quizId = (int) ($params['quiz_id'] ?? '');
    $questionId = (int) ($params['question_id'] ?? '');
    $question = $params['question'];
    $correct = (int) ($params['correct'] ?? '');
    $alternative1 = $params['alternative_1'];
    $alternative2 = $params['alternative_2'];
    $alternative3 = $params['alternative_3'];
    $alternative4 = $params['alternative_4'];
    $alternative5 = $params['alternative_5'];

    $this->path .= 'edit_quiz_question.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data['course_id'] = $courseId;
    $this->data['quiz_id'] = $quizId;
    $this->data['question_id'] = $questionId;
    $this->data['question'] = $question;
    $this->data['correct'] = $correct;
    $this->data['alternative_1'] = $alternative1;
    $this->data['alternative_2'] = $alternative2;
    $this->data['alternative_3'] = $alternative3;
    $this->data['alternative_4'] = $alternative4;
    $this->data['alternative_5'] = $alternative5;
    $this->data['err_question'] = false;
    $this->data['err_correct'] = false;
    $this->data['err_alternative_1'] = false;
    $this->data['err_alternative_2'] = false;
    $this->data['err_alternative_3'] = false;
    $this->data['err_alternative_4'] = false;
    $this->data['err_alternative_5'] = false;
    $this->data[GlobalValues::SESSION_MESSAGE] = '';
    $this->data[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;

    $quizModel = new QuizModel();
    $questionModel = new QuestionModel();

    $courseById = $this->model->getById($courseId);
    $quizByIdAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);
    $questionByIdAndQuizId = $questionModel->getByIdAndQuizId($questionId, $quizId);

    if ($_SESSION[GlobalValues::CSRF_TOKEN_IS_INVALID]) {
      $message = UserMessage::INVALID_CSRF_TOKEN;
    } elseif (empty($courseById) || empty($quizByIdAndCourseId) || empty($questionByIdAndQuizId)) {
      $message = CourseMessage::ERR_QUESTION_INEXISTENT;
    } elseif (!isValidText($question, 'question')) {
      $this->data['err_question'] = true;
      $message = CourseMessage::ERR_INVALID_QUESTION_TEXT;
    } elseif (!isValidQuestionNumber($correct)) {
      $this->data['err_correct'] = true;
      $message = CourseMessage::ERR_INVALID_QUESTION_NUMBER;
    } elseif (!isValidText($alternative1, 'alternative')) {
      $this->data['err_alternative_1'] = true;
      $message = CourseMessage::ERR_INVALID_ALTERNATIVE;
    } elseif (!isValidText($alternative2, 'alternative')) {
      $this->data['err_alternative_2'] = true;
      $message = CourseMessage::ERR_INVALID_ALTERNATIVE;
    } elseif (!isValidText($alternative3, 'alternative')) {
      $this->data['err_alternative_3'] = true;
      $message = CourseMessage::ERR_INVALID_ALTERNATIVE;
    } elseif (!isValidText($alternative4, 'alternative')) {
      $this->data['err_alternative_4'] = true;
      $message = CourseMessage::ERR_INVALID_ALTERNATIVE;
    } elseif (!isValidText($alternative5, 'alternative')) {
      $this->data['err_alternative_5'] = true;
      $message = CourseMessage::ERR_INVALID_ALTERNATIVE;
    } else {
      $this->data['err_question'] = false;
      $this->data['err_correct'] = false;
      $this->data['err_alternative_1'] = false;
      $this->data['err_alternative_2'] = false;
      $this->data['err_alternative_3'] = false;
      $this->data['err_alternative_4'] = false;
      $this->data['err_alternative_5'] = false;

      $alternatives = [];

      for ($i = 1; $i <= 5; $i++) {
        $variableName = 'alternative' . $i;

        if (isset($$variableName)) {
          $alternatives[] = [$i, $$variableName];
        }
      }

      $questionModel = new QuestionModel();
      $alternativeModel = new AlternativeModel();

      if ($questionModel->update($questionId, $question, $correct, $courseId, $quizId)) {
        $errorSavingAlternative = false;

        $alternativeModel = new AlternativeModel();

        $alternativesByQuestionId = $alternativeModel->getByQuestionId($questionId);

        foreach ($alternatives as $index => $alternative) {
          if (!$alternativeModel->update($alternativesByQuestionId[$index]->id, $alternative[1], $alternative[0], $courseId, $questionId)) {
            $errorSavingAlternative = true;
          }
        }

        if ($errorSavingAlternative) {
          $message = CourseMessage::ERR_WHEN_SAVING_ONE_OF_ALTERNATIVES;
        } else {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_UPDATE_QUESTION;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
          return $response->withHeader('Location', '/admin/course/quizzes/questions/' . $courseId . '/' . $quizId)->withHeader('Allow', 'GET')->withStatus(302);
        }
      } else {
        $message = CourseMessage::ERR_FAIL_UPDATE_QUESTION;
      }
    }

    $this->data[GlobalValues::SESSION_MESSAGE] = $message;

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function processQuestionDeleteRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');
    $quizId = (int) ($args['quiz_id'] ?? '');
    $questionId = (int) ($args['question_id'] ?? '');

    if (!isValidId($courseId) || !isValidId($quizId) || !isValidId($questionId)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_QUESTION_INEXISTENT;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $quizModel = new QuizModel();
      $questionModel = new QuestionModel();

      $courseById = $this->model->getById($courseId);
      $quizByIdAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);
      $questionByIdAndQuizId = $questionModel->getByIdAndQuizId($questionId, $quizId);

      if (empty($courseById) || empty($quizByIdAndCourseId) || empty($questionByIdAndQuizId)) {
        $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_QUESTION_INEXISTENT;
        $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
        return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $alternativeModel = new AlternativeModel();

        $alternativesByQuestionId = $alternativeModel->getByQuestionId($questionId);

        $errorWhenRemovingAnAlternative = false;

        foreach ($alternativesByQuestionId as $alternative) {
          if (!$alternativeModel->delete($alternative->id, 'id')) {
            $errorWhenRemovingAnAlternative = true;
          }
        }

        if ($errorWhenRemovingAnAlternative) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_WHEN_REMOVING_ONE_OF_ALTERNATIVES;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
        } else {
          if ($questionModel->delete($questionByIdAndQuizId->id, 'id')) {
            $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_DELETE_QUESTION;
            $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
          } else {
            $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_FAIL_DELETE_QUESTION;
            $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
          }
        }
      }
    }

    return $response->withHeader('Location', '/admin/course/quizzes/questions/' . $courseId . '/' . $quizId)->withHeader('Allow', 'GET')->withStatus(302);
  }

  public function processQuizVisibilityRequest(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courseId = (int) ($args['course_id'] ?? '');
    $quizId = (int) ($args['quiz_id'] ?? '');

    $quizModel = new QuizModel();

    $courseById = $this->model->getById($courseId);
    $quizByIdAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);

    if (!isValidId($courseId) || !isValidId($quizId)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_QUIZ_INEXISTENT;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } elseif (empty($courseById) || empty($quizByIdAndCourseId)) {
      $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_QUIZ_INEXISTENT;
      $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      return $response->withHeader('Location', '/admin/dashboard')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $questionModel = new QuestionModel();

      $questionByQuizId = $questionModel->getByQuizId($quizId) ?? [];

      $questionsNumber = count($questionByQuizId);

      if ($questionsNumber >= GlobalValues::MINIMUM_QUANTITY_QUESTIONS) {
        $visibility = (int) $quizByIdAndCourseId->visibility;

        $visibility = ($visibility == 0 ? 1 : 0);

        if ($quizModel->update($quizByIdAndCourseId->id, $quizByIdAndCourseId->title, $visibility)) {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::SUCCESS_TO_CHANGE_QUIZ_VISIBILITY;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_SUCCESS;
        } else {
          $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_FAIL_TO_CHANGE_QUIZ_VISIBILITY;
          $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
        }
      } else {
        $_SESSION[GlobalValues::SESSION_MESSAGE_CONTENT] = CourseMessage::ERR_MINIMUM_QUESTIONS_FOR_VISIBILITY;
        $_SESSION[GlobalValues::SESSION_MESSAGE_TYPE] = GlobalValues::TYPE_MSG_ERROR;
      }
    }

    return $response->withHeader('Location', '/admin/course/quizzes/' . $courseId)->withHeader('Allow', 'GET')->withStatus(302);
  }

  public function coursePage(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courses = $this->model->getActiveVisibility() ?? [];

    $courseId = (int) ($args['course_id'] ?? '');

    $this->path .= 'course_page.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data[GlobalValues::USER_IS_CONNECTED] = $_SESSION[GlobalValues::USER_IS_CONNECTED];
    $this->data['courses'] = $courses;
    $this->data['course_id'] = $courseId;

    $topicModel = new TopicModel();
    $quizModel = new QuizModel();

    $courseById = $this->model->getById($courseId);

    if (!isValidId($courseId) || empty($courseById)) {
      return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $topicsByCourseId = $topicModel->getByCourseId($courseId) ?? [];
      $quizzesByCourseId = $quizModel->getByCourseId($courseId) ?? [];

      $quizzesByVisibility = [];

      // remove quiz not visible
      foreach ($quizzesByCourseId as $quiz) {
        if ($quiz->visibility == 1) {
          $quizzesByVisibility[] = $quiz;
        }
      }

      $courseById->image = base64_encode($courseById->image);

      $this->data['course'] = $courseById;
      $this->data['topics'] = $topicsByCourseId;
      $this->data['quizzes'] = $quizzesByVisibility;
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function courseTopicPage(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courses = $this->model->getActiveVisibility() ?? [];

    $courseId = (int) ($args['course_id'] ?? '');
    $topicId = (int) ($args['topic_id'] ?? '');

    $this->path .= 'course_topic_page.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data[GlobalValues::USER_IS_CONNECTED] = $_SESSION[GlobalValues::USER_IS_CONNECTED];
    $this->data['courses'] = $courses;
    $this->data['course_id'] = $courseId;
    $this->data['topic_id'] = $topicId;

    $topicModel = new TopicModel();

    $courseById = $this->model->getById($courseId);
    $topicByIdAndCourseId = $topicModel->getByIdAndCourseId($topicId, $courseId);

    if (!isValidId($courseId) || empty($courseById) || !isValidId($topicId) || empty($topicByIdAndCourseId)) {
      return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $this->data['course'] = $courseById;
      $this->data['topic'] = $topicByIdAndCourseId;

      // next and previous
      $topicsByCourseId = $topicModel->getByCourseId($courseId) ?? [];
      $topicIds = [];

      $previousTopicId = null;
      $nextTopicId = null;

      foreach ($topicsByCourseId as $topic) {
        $topicIds[] = $topic->id;
      }

      $key = array_search($topicId, $topicIds, true);
      if ($key !== false) {
        if ($key < sizeof($topicIds) - 1) {
          $previousTopicId = $topicIds[$key + 1];
          $this->data['previous_topic_id'] = $previousTopicId;
        }

        if ($key > 0) {
          $nextTopicId = $topicIds[$key - 1];
          $this->data['next_topic_id'] = $nextTopicId;
        }
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }

  public function courseQuizPage(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
  {
    $courses = $this->model->getActiveVisibility() ?? [];

    $courseId = (int) ($args['course_id'] ?? '');
    $quizId = (int) ($args['quiz_id'] ?? '');

    $this->path .= 'course_quiz_page.html.twig';
    $this->data['page_title'] = 'NetLearnHub | Aprenda de graça TI';
    $this->data[GlobalValues::USER_IS_CONNECTED] = $_SESSION[GlobalValues::USER_IS_CONNECTED];
    $this->data['courses'] = $courses;
    $this->data['course_id'] = $courseId;
    $this->data['topic_id'] = $quizId;

    $quizModel = new QuizModel();

    $courseById = $this->model->getById($courseId);
    $quizByIdAndCourseId = $quizModel->getByIdAndCourseId($quizId, $courseId);

    if (!isValidId($courseId) || empty($courseById) || !isValidId($quizId) || empty($quizByIdAndCourseId)) {
      return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
    } elseif ($quizByIdAndCourseId->visibility == 0) {
      return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
    } else {
      $this->data['course'] = $courseById;
      $this->data['quiz'] = $quizByIdAndCourseId;

      $questionModel = new QuestionModel();
      $alternativeModel = new AlternativeModel();

      // get questions
      $questionsByQuizId = $questionModel->getByQuizId($quizId);
      if (empty($questionsByQuizId)) {
        return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        // get alternatives
        $alternativesByCourseId = $alternativeModel->getByCourseId($courseId);
      }

      if (empty($alternativesByCourseId)) {
        return $response->withHeader('Location', '/home')->withHeader('Allow', 'GET')->withStatus(302);
      } else {
        $this->data['questions'] = $questionsByQuizId;
        $this->data['alternatives'] = $alternativesByCourseId;
      }
    }

    return $this->twig->render($response, $this->path, $this->data);
  }
}