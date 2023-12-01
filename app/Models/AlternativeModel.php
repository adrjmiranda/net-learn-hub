<?php

namespace app\Models;

use app\classes\GlobalValues;

class AlternativeModel extends Model
{
  protected string $table = GlobalValues::ALTERNATIVES_TABLE;
}