<?php

use PDO;
use PDOException;

trait User
{
  public function getUserByEmail(string $email): object
  {
    $stmt = $this->connect->prepare('SELECT * FROM ' . $this->table . ' WHERE email = :email LIMIT 1');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    try {
      $stmt->execute();
      return $stmt->fetch();
    } catch (PDOException $pDOException) {
      return new stdClass();
    }
  }
}