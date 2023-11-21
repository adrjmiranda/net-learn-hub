<?php

require_once __DIR__ . '/../functions/helpers.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Stream;

$allowedStaticFileTypes = ['css', 'js', 'images', 'favicon.ico'];

$app->get('/public/{type}[/{file}]', function (Request $request, Response $response, array $args) use ($allowedStaticFileTypes) {
  $basePath = __DIR__ . '/../..' . '/public/';

  $type = $args['type'];
  if (!in_array($type, $allowedStaticFileTypes)) {
    return $response->withStatus(404);
  }

  $file = isset($args['file']) ? $basePath . $type . '/' . $args['file'] : $basePath . $type;

  if (file_exists($file)) {
    $contentType = getContentType($type);
    return serveStaticFile($response, $file, $contentType);
  } else {
    return $response->withStatus(404);
  }
});

function serveStaticFile(Response $response, string $file, string $contentType): Response
{
  $fh = fopen($file, 'r');
  $stream = new Stream($fh);

  if ($fh === false) {
    return $response->withStatus(500);
  }

  return $response->withHeader('Content-Type', $contentType)->withBody($stream);
}