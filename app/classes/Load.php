<?php

namespace app\classes;

class Load
{
  public static function file(string $file): mixed
  {
    $file = getPath() . $file;

    if (!file_exists($file)) {
      throw new \Exception('Esse arquivo não existe: ' . $file);
    }

    return require $file;
  }
}