<?php

declare(strict_types=1);

namespace App\Controller;

use App\View\ViewRenderer;

abstract class AbstractController
{
    public function __construct(protected readonly ViewRenderer $view)
    {
    }

    protected function render(string $view, array $data = []): string
    {
        return $this->view->render($view, $data);
    }

    protected function redirect(string $url, array $data = [], int $status = 200): string
    {
        return $this->view->redirect($url, $data, $status);
    }

    protected function flash(string $type, string $message): void
    {
        $this->view->setFlash($type, $message);
    }

    protected function error(int $status, string $message): string
    {
        http_response_code($status);
        return $this->render('error/' . $status, ['title' => $message, 'message' => $message]);
    }
}
