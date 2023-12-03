<?php

namespace app\Models;

use app\classes\GlobalValues;
use PDO;
use PDOException;

class QuestionModel extends Model
{
  protected string $table = GlobalValues::QUESTIONS_TABLE;

  public function store(string $question, int $correct, int $courseId, int $quizId): bool
  {
    $stmt = $this->connect->prepare('INSERT INTO ' . $this->table . ' (question, correct, course_id, quiz_id) VALUES (:question, :correct, :course_id, :quiz_id)');

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