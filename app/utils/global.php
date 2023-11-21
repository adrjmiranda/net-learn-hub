<?php

$baseURL = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI'] . '?');

return [
  'baseURL' => $baseURL
];