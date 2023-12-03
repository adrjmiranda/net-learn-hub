<?php

namespace app\Models;

use app\classes\GlobalValues;
use PDO;
use PDOException;

class AlternativeModel extends Model
{
  protected string $table = GlobalValues::ALTERNATIVES_TABLE;

  public function store(string $alternative, int $alternativeNumber, int $courseId, int $questionId): bool
  {
    $stmt = $this->connect->prepare('INSERT INTO ' . $this->table . ' (alternative, alternative_number, course_id, question_id) VALUES (:alternative, :alternative_number, :course_id, :question_id)');

    $stmt->bindParam(':alternative', $alternative, PDO::PARAM_STR);
    $stmt->bindParam(':alternative_number', $alternativeNumber, PDO::PARAM_INT);
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
    $stmt->bindParam(':question_id', $questionId, PDO::PARAM_INT);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }
}