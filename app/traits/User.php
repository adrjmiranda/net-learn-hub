<?php

namespace app\traits;

require_once __DIR__ . '/../functions/helpers.php';

use PDOException;
use app\classes\SearchQueryOptions;
use stdClass;

trait User
{
  public function getUserByEmail(string $email): mixed
  {
    try {
      $searchQueryOptions = new SearchQueryOptions();

      $searchQueryOptions->limit = 1;
      $searchQueryOptions->type = SearchQueryOptions::SPECIFIC;
      $searchQueryOptions->conditions = [
        'column_name' => 'email',
        'operator' => SearchQueryOptions::EQUAL_OPERATOR,
        'values' => $email
      ];
      ;

      $stmt = prepareSearchStatement($this->connect, $this->table, $searchQueryOptions);
      $stmt->execute();
      return $stmt->fetch();
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
      return new stdClass();
    }
  }
}