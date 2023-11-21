<?php

namespace app\traits;

require_once __DIR__ . '/../utils/global.php';

trait View
{
  protected $twig;
  protected $baseURL;

  protected function twig()
  {
    $loader = new \Twig\Loader\FilesystemLoader(dirname(__FILE__) . '/../Views');
    $this->twig = new \Twig\Environment($loader);
  }

  protected function functions()
  {
  }

  protected function load()
  {
    $this->twig();
    $this->functions();
  }

  protected function view(
    string $view,
    array $data
  ) {
    $this->load();

    $data['baseURL'] = $this->baseURL;
    echo $this->twig->render($view, $data);
  }
}
