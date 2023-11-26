<?php

namespace app\traits;

require_once __DIR__ . '/../functions/helpers.php';

use PDOException;
use app\classes\SearchQueryOptions;

trait User
{
  public function getUserByEmail(string $email): ?object
  {
    $user = null;

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

      if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch();
      }
    } catch (PDOException $pDOException) {
      echo $pDOException->getMessage();
    }

    return $user;
  }
}