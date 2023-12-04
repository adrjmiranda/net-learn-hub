<?php

namespace app\Models;

use app\classes\GlobalValues;
use PDO;
use PDOException;

class QuestionModel extends Model
{
  protected string $table = GlobalValues::QUESTIONS_TABLE;

  public function store(string $question, int $correct, int $courseId, int $quizId): ?object
  {
    $stmt = $this->connect->prepare('INSERT INTO ' . $this->table . ' (question, correct, course_id, quiz_id) VALUES (:question, :correct, :course_id, :quiz_id)');

    $stmt->bindParam(':question', $question, PDO::PARAM_STR);
    $stmt->bindParam(':correct', $correct, PDO::PARAM_INT);
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    $stmt->bindParam(':quiz_id', $quizId, PDO::PARAM_INT);

    try {
      $stmt->execute();
      $lastInsertedId = $this->connect->lastInsertId();

      $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
      $stmt = $this->connect->prepare($query);
      $stmt->bindParam(':id', $lastInsertedId, PDO::PARAM_INT);
      $stmt->execute();

      $row = $stmt->fetch();

      return $row ? $row : null;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return null;
    }
  }

  public function update(int $id, string $question, int $correct, int $courseId, int $quizId): bool
  {
    $stmt = $this->connect->prepare('UPDATE ' . $this->table . ' SET question = :question, correct = :correct, course_id = :course_id, quiz_id = :quiz_id WHERE id = :id');

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':question', $question, PDO::PARAM_STR);
    $stmt->bindParam(':correct', $correct, PDO::PARAM_INT);
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    $stmt->bindParam(':quiz_id', $quizId, PDO::PARAM_INT);


    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }
}