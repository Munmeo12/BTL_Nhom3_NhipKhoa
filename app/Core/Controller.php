<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    public function __construct(protected readonly View $view = new View())
    {
    }

    protected function render(string $template, array $data = [], ?string $layout = null): void
    {
        $this->view->render($template, $data, $layout);
    }
}
