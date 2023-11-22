<?php

namespace app\Models;

use PDO;
use PDOStatement;
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

  private function prepareStatement(?int $limit): PDOStatement
  {
    $query = 'SELECT * FROM ' . $this->table . ' ORDER BY id DESC';
    if ($limit !== null && $limit > 0) {
      $query .= ' LIMIT :limit';
    }

    $stmt = $this->connect->prepare($query);

    if ($limit !== null && $limit > 0) {
      $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    }

    return $stmt;
  }

  public function all(?int $limit = null): array
  {
    try {
      $stmt = $this->prepareStatement($limit);
      $stmt->execute();
      return $stmt->fetchAll();
    } catch (PDOException $pDOException) {
      return [];
    }
  }
}