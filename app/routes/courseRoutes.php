<?php

use Slim\App;

return function (App $app) {
  $app->get('/', 'CourseController:index');
};