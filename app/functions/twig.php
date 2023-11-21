<?php

$file_exists = new \Twig\TwigFunction('file_exists', function (string $file_name) {
  return file_exists($file_name);
});

$test = new \Twig\TwigFunction('test', function () {
  echo 'test';
});

return [
  $file_exists,
  $test
];