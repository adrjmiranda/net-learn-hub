<?php

namespace app\Models;

require_once __DIR__ . '/../functions/helpers.php';

use PDO;
use PDOException;
use app\classes\Connection;
use app\classes\SearchQueryOptions;

class Model
{
  protected PDO $connect;
  protected string $table;

  public function __construct()
  {
    $this->connect = Connection::connect();
  }

  public function getTable(): string
  {
    return $this->table;
  }

  public function all(?int $limit = null): ?array
  {
    $data = null;

    try {
      $searchQueryOptions = new SearchQueryOptions();

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetchAll();

        if (!empty($data)) {
          foreach ($data as $object) {
            if (property_exists($object, 'image')) {
              $object->image = base64_encode($object->image);
            }
          }
        }
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getByTitle(string $title): ?object
  {
    $data = null;

    try {
      $searchQueryOptions = new SearchQueryOptions();

      $searchQueryOptions->limit = 1;
      $searchQueryOptions->type = SearchQueryOptions::SPECIFIC;
      $searchQueryOptions->conditions = [
        'column_name' => 'title',
        'operator' => SearchQueryOptions::EQUAL_OPERATOR,
        'values' => $title
      ];

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch();
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getById(int $id): ?object
  {
    $data = null;

    try {
      $searchQueryOptions = new SearchQueryOptions();

      $searchQueryOptions->limit = 1;
      $searchQueryOptions->type = SearchQueryOptions::SPECIFIC;
      $searchQueryOptions->conditions = [
        'column_name' => 'id',
        'operator' => SearchQueryOptions::EQUAL_OPERATOR,
        'values' => $id
      ];

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch();
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getByCourseId(int $courseId): ?array
  {
    $data = null;

    try {
      $searchQueryOptions = new SearchQueryOptions();

      $searchQueryOptions->type = SearchQueryOptions::SPECIFIC;
      $searchQueryOptions->conditions = [
        'column_name' => 'course_id',
        'operator' => SearchQueryOptions::EQUAL_OPERATOR,
        'values' => $courseId
      ];

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetchAll();

        if (!empty($data)) {
          foreach ($data as $object) {
            if (property_exists($object, 'image')) {
              $object->image = base64_encode($object->image);
            }
          }
        }
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getByQuizId(int $quizId): ?array
  {
    $data = null;

    try {
      $searchQueryOptions = new SearchQueryOptions();

      $searchQueryOptions->type = SearchQueryOptions::SPECIFIC;
      $searchQueryOptions->conditions = [
        'column_name' => 'quiz_id',
        'operator' => SearchQueryOptions::EQUAL_OPERATOR,
        'values' => $quizId
      ];

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetchAll();

        if (!empty($data)) {
          foreach ($data as $object) {
            if (property_exists($object, 'image')) {
              $object->image = base64_encode($object->image);
            }
          }
        }
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getByQuestionId(int $questionId): ?array
  {
    $data = null;

    try {
      $searchQueryOptions = new SearchQueryOptions();

      $searchQueryOptions->type = SearchQueryOptions::SPECIFIC;
      $searchQueryOptions->conditions = [
        'column_name' => 'question_id',
        'operator' => SearchQueryOptions::EQUAL_OPERATOR,
        'values' => $questionId
      ];
      $searchQueryOptions->order = SearchQueryOptions::ASC;

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetchAll();

        if (!empty($data)) {
          foreach ($data as $object) {
            if (property_exists($object, 'image')) {
              $object->image = base64_encode($object->image);
            }
          }
        }
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getByIdAndCourseId(int $id, int $courseId): ?object
  {
    $data = null;

    try {
      $stmt = $this->connect->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id AND course_id = :course_id LIMIT 1');
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch();
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getByQuizIdAndCourseId(int $quizId, int $courseId): ?object
  {
    $data = null;

    try {
      $stmt = $this->connect->prepare('SELECT * FROM ' . $this->table . ' WHERE quiz_id = :quiz_id AND course_id = :course_id LIMIT 1');
      $stmt->bindParam(':quiz_id', $quizId, PDO::PARAM_INT);
      $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch();
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getByIdAndQuizId(int $id, int $quizId): ?object
  {
    $data = null;

    try {
      $stmt = $this->connect->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id AND quiz_id = :quiz_id LIMIT 1');
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->bindParam(':quiz_id', $quizId, PDO::PARAM_INT);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch();
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getByTitleAndCourseId(string $title, int $courseId): ?object
  {
    $data = null;

    try {
      $stmt = $this->connect->prepare('SELECT * FROM ' . $this->table . ' WHERE title = :title AND course_id = :course_id LIMIT 1');
      $stmt->bindParam(':title', $title, PDO::PARAM_STR);
      $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch();
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function delete(int $id, string $column): bool
  {
    $stmt = $this->connect->prepare('DELETE FROM ' . $this->table . ' WHERE ' . $column . ' = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    try {
      if ($stmt->execute()) {
        return true;
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return false;
  }
}