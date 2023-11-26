<?php

namespace app\Models;

require_once __DIR__ . '/../functions/helpers.php';

use PDO;
use PDOException;
use app\classes\Connection;
use app\classes\SearchQueryOptions;

class Model
{
  protected PDO $connect;
  protected string $table;

  public function __construct()
  {
    $this->connect = Connection::connect();
  }

  public function getTable(): string
  {
    return $this->table;
  }

  public function all(?int $limit = null): ?array
  {
    $data = null;

    try {
      $searchQueryOptions = new SearchQueryOptions();

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetchAll();
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }
}