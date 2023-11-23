<?php

namespace app\Models;

use stdClass;
use PDO;
use PDOException;
use app\traits\User;

class AdministratorModel extends Model
{
  use User;
  protected string $table = 'administrators';
}