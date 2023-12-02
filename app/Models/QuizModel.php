<?php

namespace app\Models;

use app\classes\GlobalValues;
use PDO;
use PDOException;

class QuizModel extends Model
{
  protected string $table = GlobalValues::QUIZZES_TABLE;

  public function store(string $title, int $courseId): bool
  {
    $stmt = $this->connect->prepare('INSERT INTO ' . $this->table . ' (title, course_id) VALUES (:title, :course_id)');

    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }

  public function update(int $id, string $title): bool
  {
    $stmt = $this->connect->prepare('UPDATE ' . $this->table . ' SET title = :title WHERE id = :id');

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }
}