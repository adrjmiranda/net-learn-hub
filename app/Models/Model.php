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

        if (!empty($data)) {
          foreach ($data as $object) {
            if (property_exists($object, 'image')) {
              $object->image = base64_encode($object->image);
            }
          }
        }
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getByTitle(string $title): ?object
  {
    $data = null;

    try {
      $searchQueryOptions = new SearchQueryOptions();

      $searchQueryOptions->limit = 1;
      $searchQueryOptions->type = SearchQueryOptions::SPECIFIC;
      $searchQueryOptions->conditions = [
        'column_name' => 'title',
        'operator' => SearchQueryOptions::EQUAL_OPERATOR,
        'values' => $title
      ];
      ;

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch();
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }

  public function getById(string $id): ?object
  {
    $data = null;

    try {
      $searchQueryOptions = new SearchQueryOptions();

      $searchQueryOptions->limit = 1;
      $searchQueryOptions->type = SearchQueryOptions::SPECIFIC;
      $searchQueryOptions->conditions = [
        'column_name' => 'id',
        'operator' => SearchQueryOptions::EQUAL_OPERATOR,
        'values' => $id
      ];
      ;

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
        $data = $stmt->fetch();
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $data;
  }
}