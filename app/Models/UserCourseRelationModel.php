<?php

namespace app\Models;

use app\classes\GlobalValues;

class UserCourseRelationModel extends Model
{
  protected string $table = GlobalValues::users_courses_relation;
}