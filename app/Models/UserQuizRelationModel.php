<?php

namespace app\Models;

use app\classes\GlobalValues;

class UserQuizRelationModel extends Model
{
  protected string $table = GlobalValues::users_quizzes_relation;
}