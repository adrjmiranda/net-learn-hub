<?php
use Slim\Routing\RouteCollectorProxy;

$responseFactory = $dependencies['response_factory'];
$twig = $dependencies['twig'];

$app->group('/', function (RouteCollectorProxy $group) use ($twig) {
  $group->get('terms-and-conditions', function ($request, $response, $args) use ($twig) {
    return $twig->render($response, '/pages/others/terms_and_conditions.html.twig', []);
  });

  $group->get('privacy-policy', function ($request, $response, $args) use ($twig) {
    return $twig->render($response, '/pages/others/privacy_policy.html.twig', []);
  });
});