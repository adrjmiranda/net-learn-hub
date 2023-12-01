<?php

namespace app\Models;

use app\classes\GlobalValues;

class UserModel extends Model
{
  protected string $table = GlobalValues::USERS_TABLE;
}