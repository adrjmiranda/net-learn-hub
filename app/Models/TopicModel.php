<?php

namespace app\Models;

use app\classes\GlobalValues;
use PDO;
use PDOException;

class TopicModel extends Model
{
  protected string $table = GlobalValues::COURSES_TOPICS_TABLE;

  public function store(string $title, mixed $content, int $courseId): bool
  {
    $stmt = $this->connect->prepare('INSERT INTO ' . $this->table . ' (title, content, course_id) VALUES (:title, :content, :course_id)');

    $stmt->bindParam(':content', $content, PDO::PARAM_LOB);
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
}