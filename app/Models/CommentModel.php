<?php

namespace app\Models;

use app\classes\GlobalValues;

class CommentModel extends Model
{
  protected string $table = GlobalValues::COMMENTS_TABLE;
}