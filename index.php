<?php

$dependencies = require_once __DIR__ . '/bootstrap.php';

$dotenv = $dependencies['dotenv'];
$app = $dependencies['slim_app'];

$app->get('/', 'app\Controllers\CourseController:index');


// Rota para servir arquivos CSS
$app->get('/public/css/{file}', function ($request, $response, $args) {
  $file = __DIR__ . '/public/css/' . $args['file'];

  return serveStaticFile($response, $file, 'text/css');
});

// Rota para servir arquivos JavaScript
$app->get('/public/js/{file}', function ($request, $response, $args) {
  $file = __DIR__ . '/public/js/' . $args['file'];

  return serveStaticFile($response, $file, 'application/javascript');
});

// Rota para servir arquivos de Imagens
$app->get('/public/images/{file}', function ($request, $response, $args) {
  $file = __DIR__ . '/public/images/' . $args['file'];

  $extension = explode('.', $args['file'])[1];

  return serveStaticFile($response, $file, 'image/' . $extension);
});

// Função para servir arquivos estáticos
function serveStaticFile($response, $file, $contentType)
{
  if (file_exists($file)) {
    $fh = fopen($file, 'r');
    $stream = new \Slim\Psr7\Stream($fh);

    return $response
      ->withHeader('Content-Type', $contentType)
      ->withBody($stream);
  } else {
    return $response->withStatus(404);
  }
}

// Configuração para servir o arquivo favicon.ico da pasta public
$app->get('/public/favicon.ico', function ($request, $response) {
  $file = __DIR__ . '/public/favicon.ico';

  if (file_exists($file)) {
    $fh = fopen($file, 'r');
    $stream = new \Slim\Psr7\Stream($fh);

    return $response
      ->withHeader('Content-Type', 'image/x-icon')
      ->withBody($stream);
  } else {
    return $response->withStatus(404);
  }
});

$app->run();