<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/admin', 'app\Controllers\AdministratorController:index');
$app->post('/admin/login', 'app\Controllers\AdministratorController:login');