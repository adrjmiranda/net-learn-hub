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

  public function update(int $id, string $alternative, int $alternativeNumber, int $courseId, int $questionId): bool
  {
    $stmt = $this->connect->prepare('UPDATE ' . $this->table . ' SET alternative = :alternative, alternative_number = :alternative_number, course_id = :course_id, question_id = :question_id WHERE id = :id');

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
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