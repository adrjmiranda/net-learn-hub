<?php

namespace app\Models;

use app\classes\GlobalValues;
use PDO;
use PDOException;

class UserModel extends Model {
  protected string $table = GlobalValues::USERS_TABLE;

  public function store(string $email, string $firstName, string $lastName, string $image): bool {
    $stmt = $this->connect->prepare('INSERT INTO '.$this->table.' (email, first_name, last_name, image) VALUES (:email, :first_name, :last_name, :image)');

    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
    $stmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
    $stmt->bindParam(':image', $image, PDO::PARAM_STR);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }

  public function update(int $id, string $firstName, string $lastName, string $image): bool {
    $stmt = $this->connect->prepare('UPDATE '.$this->table.' SET first_name = :first_name, last_name = :last_name, image = :image WHERE id = :id');

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':title', $firstName, PDO::PARAM_STR);
    $stmt->bindParam(':workload', $lastName, PDO::PARAM_STR);
    $stmt->bindParam(':description', $image, PDO::PARAM_STR);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return false;
    }
  }
}