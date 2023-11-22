<?php

namespace app\Models;

require_once __DIR__ . '/../functions/helpers.php';

use PDO;
use PDOStatement;
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

  private function prepareSearchStatement(SearchQueryOptions $options): PDOStatement
  {
    $query = 'SELECT ' . (empty($options->columns) ? '*' : implode(', ', $options->columns)) . ' FROM ' . $this->table;

    if (
      $options->type === SearchQueryOptions::SPECIFIC &&
      !empty($options->conditions['columnName']) &&
      !empty($options->conditions['operator']) &&
      !empty($options->conditions['values'])
    ) {
      $validOperator = in_array($options->conditions['operator'], SearchQueryOptions::getAllowedOperators());

      if ($validOperator) {
        switch ($options->conditions['operator']) {
          case SearchQueryOptions::BETWEEN_OPERATOR:
            $query .= ' WHERE ' . $options->conditions['columnName'] . ' BETWEEN :value1 AND :value2';
            break;

          case SearchQueryOptions::LIKE_OPERATOR:
            $query .= ' WHERE ' . $options->conditions['columnName'] . ' LIKE :value';
            break;

          default:
            $query .= ' WHERE ' . $options->conditions['columnName'] . ' ' . $options->conditions['operator'] . ' :value';
            break;
        }
      }
    }

    $query .= (in_array($options->order, SearchQueryOptions::getAllowedOrder()) ? ' ORDER BY id ' . $options->order : ' ORDER BY id DESC');

    if ($options->limit !== null && $options->limit > 0) {
      $query .= ' LIMIT :limit';
    }

    $stmt = $this->connect->prepare($query);

    if (!empty($options->columns)) {
      foreach ($options->columns as $key => $column) {
        $stmt->bindParam(':column' . $key, $column, PDO::PARAM_STR);
      }
    }

    if (
      $options->type === SearchQueryOptions::SPECIFIC &&
      !empty($options->conditions['columnName']) &&
      !empty($options->conditions['operator']) &&
      !empty($options->conditions['values'])
    ) {
      if ($validOperator) {
        switch ($options->conditions['operator']) {
          case SearchQueryOptions::BETWEEN_OPERATOR:
            $stmt->bindParam(':value1', $options->conditions['values'][0], PDO::PARAM_STR);
            $stmt->bindParam(':value2', $options->conditions['values'][1], PDO::PARAM_STR);
            break;

          case SearchQueryOptions::LIKE_OPERATOR:
            $stmt->bindParam(':value', $options->conditions['values'], PDO::PARAM_STR);
            break;

          default:
            $stmt->bindParam(':value', $options->conditions['values'], PDO::PARAM_STR);
            break;
        }
      }
    }

    if ($options->limit !== null && $options->limit > 0) {
      $stmt->bindParam(':limit', $options->limit, PDO::PARAM_INT);
    }

    return $stmt;
  }


  public function all(?int $limit = null): array
  {
    try {
      $searchQueryOptions = new SearchQueryOptions();

      $stmt = $this->prepareSearchStatement($searchQueryOptions);
      $stmt->execute();
      return $stmt->fetchAll();
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return [];
    }
  }
}