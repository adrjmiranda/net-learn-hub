<?php

namespace app\Models;

use app\classes\GlobalValues;

class QuestionModel extends Model
{
  protected string $table = GlobalValues::QUESTIONS_TABLE;
}