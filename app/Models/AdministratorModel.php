<?php

namespace app\Models;

use stdClass;
use PDO;
use PDOException;
use app\traits\User;
use app\classes\GlobalValues;

class AdministratorModel extends Model
{
  use User;
  protected string $table = GlobalValues::ADMINISTRATORS_TABLE;

  public function update(int $id, string $firstName, string $lastName, string $password, string $image, string $description): bool
  {
    $stmt = $this->connect->prepare('UPDATE ' . $this->table . ' SET first_name = :first_name, last_name = :last_name, password = :password, image = :image, description = :description WHERE id = :id');

    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
    $stmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':image', $image, PDO::PARAM_STR);
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