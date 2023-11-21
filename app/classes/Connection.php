<?php

namespace app\classes;

use PDO;

class Connection
{
  public static function connect(): PDO
  {
    $pdo = new PDO('mysql:host=netlearnhubdb;dbname=netlearnhub', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    return $pdo;
  }
}