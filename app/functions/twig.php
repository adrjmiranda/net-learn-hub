<?php

use Twig\TwigFunction;

$file_exists = new TwigFunction('file_exists', function (string $file_name) {
  return file_exists($file_name);
});

return [
  $file_exists
];