<?php

namespace app\traits;

require_once __DIR__ . '/../functions/helpers.php';

use PDOException;
use app\classes\SearchQueryOptions;
use stdClass;

trait User
{
  public function getUserByEmail(string $email): object
  {
    try {
      $searchQueryOptions = new SearchQueryOptions();

      $searchQueryOptions->limit = 1;
      $searchQueryOptions->type = SearchQueryOptions::SPECIFIC;
      $searchQueryOptions->conditions = [
        'columnName' => 'email',
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

  public function authUser(string $password, string $hash): void
  {
    if (password_verify($password, $hash)) {
      $token = bin2hex(random_bytes(32));

      switch ($this->table) {
        case 'administrators':
          $_SESSION['adminToken'] = $token;
          break;

        case 'users':
          $_SESSION['userToken'] = $token;
          break;

        default:
          return;
      }
    }
  }
}