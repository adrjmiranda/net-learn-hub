<?php

namespace app\Models;

use PDO;
use app\classes\Connection;

class Model
{
  protected PDO $connect;
  protected string $table;

  public function __construct()
  {
    $this->connect = Connection::connect();
  }

  public function all()
  {
    $sql = 'SELECT * FROM ' . $this->table;
    $all = $this->connect->prepare($sql);
    $all->execute();

    return $all->fetchAll();
  }
}