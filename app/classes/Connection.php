<?php

namespace app\classes;

use PDO;

class Connection
{
  public static function connect(): PDO
  {
    $pdo = new PDO('mysql:host=' . $_ENV['HOST_NAME'] . ';dbname=' . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    return $pdo;
  }
}