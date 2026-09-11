<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Service\AuthService;
use App\Service\CsrfService;
use App\View\ViewRenderer;

abstract class AbstractController
{
    public function __construct(
        protected readonly ViewRenderer $view,
        protected readonly AuthService $auth,
        protected readonly CsrfService $csrf
    ) {
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

    protected function csrfToken(): string
    {
        return $this->csrf->getToken();
    }

    protected function user(): ?User
    {
        return $this->auth->user();
    }

    protected function isAdmin(): bool
    {
        return $this->auth->isAdmin();
    }

    protected function getPage(int $default = 1): int
    {
        return filter_var($_GET['page'] ?? $default, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: $default;
    }
}
