<?php

namespace app\Models;

use app\classes\GlobalValues;
use PDO;
use PDOException;

class CourseModel extends Model
{
  protected string $table = GlobalValues::COURSES_TABLE;

  public function store(mixed $image, string $title, string $description): bool
  {
    $stmt = $this->connect->prepare('INSERT INTO ' . $this->table . ' (image, title, description) VALUES (:image, :title, :description)');

    $stmt->bindParam(':image', $image, PDO::PARAM_LOB);
    $stmt->bindParam(':title', $title, PDO::PARAM_LOB);
    $stmt->bindParam(':description', $description, PDO::PARAM_LOB);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }
}