<?php

namespace app\Models;

use PDO;
use PDOException;
use app\classes\Connection;

class Model
{
  protected PDO $connect;
  protected string $table;

  public function __construct()
  {
    $this->connect = Connection::connect();
  }

  public function all(int|null $limit = null)
  {
    $all = [];
    $stmt = null;

    if ($limit > 0 && $limit) {
      $query = 'SELECT * FROM' . ' ' . $this->table . ' ' . 'ORDER BY id DESC' . ' ' . 'LIMIT :limit';
      $stmt = $this->connect->prepare($query);
      $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    } else {
      $stmt = $this->connect->prepare('SELECT * FROM' . ' ' . $this->table . ' ' . 'ORDER BY id DESC');
    }

    try {
      $stmt->execute();
      $all = $stmt->fetchAll();
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $all;
  }
}