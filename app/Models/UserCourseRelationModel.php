<?php

namespace app\Models;

use app\classes\GlobalValues;

class UserCourseRelationModel extends Model
{
  protected string $table = GlobalValues::USERS_COURSES_RELATION_TABLE;
}