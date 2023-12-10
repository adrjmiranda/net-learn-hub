<?php

namespace app\Models;

use app\classes\GlobalValues;
use app\classes\SearchQueryOptions;
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

  public function update(int $id, mixed $image, string $title, int $workload, string $description): bool
  {
    if ($image) {
      $stmt = $this->connect->prepare('UPDATE ' . $this->table . ' SET image = :image, title = :title, workload = :workload, description = :description WHERE id = :id');
    } else {
      $stmt = $this->connect->prepare('UPDATE ' . $this->table . ' SET title = :title, workload = :workload, description = :description WHERE id = :id');
    }

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':workload', $workload, PDO::PARAM_INT);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);

    if ($image) {
      $stmt->bindParam(':image', $image, PDO::PARAM_LOB);
    }

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }

  public function setVisibility(int $id, int $visibility): bool
  {
    $stmt = $this->connect->prepare('UPDATE ' . $this->table . ' SET visibility = :visibility WHERE id = :id');

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':visibility', $visibility, PDO::PARAM_INT);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }
}