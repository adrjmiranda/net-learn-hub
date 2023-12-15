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
  // 

  private static ?PDO $pdoInstance = null;

  private function __construct()
  {
    // Bloqueia a criação de instâncias desta classe fora da própria classe
  }

  public static function getInstance(): PDO
  {
    if (self::$pdoInstance === null) {
      self::$pdoInstance = new PDO(
        'mysql:host=' . $_ENV['HOST_NAME'] . ';dbname=' . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
      );

      self::$pdoInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
      self::$pdoInstance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

    return self::$pdoInstance;
  }
}
