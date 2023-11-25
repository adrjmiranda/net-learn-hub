<?php

namespace app\Models;

use stdClass;
use PDO;
use PDOException;
use app\traits\User;
use app\classes\GlobalValues;

class AdministratorModel extends Model
{
  use User;
  protected string $table = GlobalValues::ADMINISTRATORS_TABLE;
}