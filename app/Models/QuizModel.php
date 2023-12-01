<?php

namespace app\Models;

use app\classes\GlobalValues;

class QuizModel extends Model
{
  protected string $table = GlobalValues::QUIZZES_TABLE;
}