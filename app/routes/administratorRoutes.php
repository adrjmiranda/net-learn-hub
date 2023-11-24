<?php

require_once __DIR__ . '/../functions/helpers.php';

use Slim\Routing\RouteCollectorProxy;

$table = 'administrators';

$app->group('/admin', function (RouteCollectorProxy $group) {
  $group->get('/login', 'app\Controllers\AdministratorController:index');
  $group->post('/login', 'app\Controllers\AdministratorController:login');

  $group->get('/dashboard', 'app\Controllers\AdministratorController:dashboard');
  $group->get('/logout', 'app\Controllers\AdministratorController:logout');
})->add(function ($request, $handler) use ($table) {
  return verifyTokenMiddleware($request, $handler, $table);
});