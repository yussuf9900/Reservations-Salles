<?php

declare(strict_types=1);

namespace App\View;

use App\Http\Strategy\ResponseStrategyInterface;
use App\Session\SessionManagerInterface;

final class ViewRenderer
{
    public function __construct(
        private readonly ResponseStrategyInterface $strategy,
        private readonly SessionManagerInterface $session
    ) {
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function setFlash(string $type, string $message): void
    {
        $this->session->flash($type, $message);
    }

    public function render(string $view, array $data = [], string $layout = 'layout/base'): string
    {
        return $this->strategy->render($view, $data, $layout);
    }

    public function redirect(string $url, array $data = [], int $status = 200): string
    {
        return $this->strategy->redirect($url, $data, $status);
    }
}
