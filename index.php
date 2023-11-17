<?php

$dependencies = require_once __DIR__ . '/bootstrap.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$dotenv = $dependencies['dotenv'];
$twig = $dependencies['twig'];
$app = $dependencies['slim_app'];

echo $twig->render('/pages/courses/home.twig', []);