<?php

namespace app\traits;

use app\classes\Load;

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

trait View
{
  protected Environment $twig;

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
  }

  protected function view(
    string $view,
    array $data
  ): void {
    $this->load();
    echo $this->twig->render($view, $data);
  }
}