<?php

namespace app\traits;

use app\classes\Load;

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

trait View
{
  protected Environment $twig;
  private string $baseURL;

  protected function twig(): void
  {
    $loader = new FilesystemLoader(dirname(__FILE__) . '/../Views');
    $this->twig = new Environment($loader);
  }

  protected function functions(): void
  {
    $functions = Load::file('/app/functions/twig.php');

    foreach ($functions as $function) {
      $this->twig->addFunction($function);
    }
  }

  protected function load(): void
  {
    $this->twig();
    $this->functions();

    $dependencies = require __DIR__ . '/../../bootstrap.php';
    $this->baseURL = $dependencies['baseURL'];
  }

  protected function view(
    string $view,
    array $data
  ): void {
    $this->load();

    $data['baseURL'] = $this->baseURL;
    echo $this->twig->render($view, $data);
  }
}
