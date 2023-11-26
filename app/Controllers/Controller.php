<?php

namespace app\Controllers;

class Controller
{
  protected object $model;

  public function getTable(): string
  {
    return $this->model->getTable();
  }
}