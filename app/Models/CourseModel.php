<?php

namespace app\Models;

use app\classes\GlobalValues;

class CourseModel extends Model
{
  protected string $table = GlobalValues::COURSES_TABLE;
}