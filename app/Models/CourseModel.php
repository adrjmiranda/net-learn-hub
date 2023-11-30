<?php

namespace app\Models;

use app\classes\GlobalValues;
use PDO;
use PDOException;

class CourseModel extends Model
{
  protected string $table = GlobalValues::COURSES_TABLE;

  public function store(mixed $image, string $title, int $workload, string $description): bool
  {
    $stmt = $this->connect->prepare('INSERT INTO ' . $this->table . ' (image, title, workload, description) VALUES (:image, :title, :workload, :description)');

    $stmt->bindParam(':image', $image, PDO::PARAM_LOB);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':workload', $workload, PDO::PARAM_INT);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }
}