<?php

namespace app\Models;

use app\classes\GlobalValues;

class UserQuizRelationModel extends Model
{
  protected string $table = GlobalValues::USERS_QUIZZES_RELATION_TABLE;
}