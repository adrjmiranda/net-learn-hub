<?php

function getPath(): string
{
  $vendorDir = dirname(__DIR__);
  return dirname($vendorDir);
}

function getContentType(string $type): string
{
  $contentType = '';

  switch ($type) {
    case 'css':
      $contentType = 'text/css';
      break;

    case 'js':
      $contentType = 'application/javascript';
      break;

    case 'images':
      $contentType = 'image/' . $type;
      break;

    case 'favicon.ico':
      $contentType = 'image/x-icon';
      break;
  }

  return $contentType;
}