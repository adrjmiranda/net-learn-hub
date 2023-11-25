<?php

namespace app\classes;

use PDO;

/**
 * Represents a PDO database connection.
 */
class Connection
{
  /**
   * Establishes a connection to the database and returns a PDO object.
   *
   * @return PDO A PDO object representing the database connection.
   */
  public static function connect(): PDO
  {
    $pdo = new PDO(
      'mysql:host=' . $_ENV['HOST_NAME'] . ';dbname=' . $_ENV['DB_NAME'],
      $_ENV['DB_USER'],
      $_ENV['DB_PASS']
    );

    // TODO: Define the environment
    // In development environment
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // In production environment
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    return $pdo;
  }
}
