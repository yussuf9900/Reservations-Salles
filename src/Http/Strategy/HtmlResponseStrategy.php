<?php

declare(strict_types=1);

namespace App\Http\Strategy;

use App\Service\AuthService;
use App\Service\CsrfService;
use App\Session\SessionManagerInterface;
use App\View\ViewRenderer;
use RuntimeException;

final class HtmlResponseStrategy implements ResponseStrategyInterface
{
    private string $templateDir;

    public function __construct(
        private readonly SessionManagerInterface $session,
        private readonly CsrfService $csrf,
        private readonly AuthService $auth,
        ?string $templateDir = null
    ) {
        $this->templateDir = $templateDir ?? dirname(__DIR__, 3) . '/templates';
    }

    public function render(string $view, array $data = [], string $layout = 'layout/base'): string
    {
        header('Content-Type: text/html; charset=utf-8');
        $data['flashes'] = $this->session->flashes();
        $data['currentUser'] = http_response_code() >= 500 ? null : $this->auth->user();
        $data['csrf_token'] = $this->csrf->getToken();
        $content = $this->renderViewOnly($view, $data);
        if ($layout === '') {
            return $content;
        }
        $data['content'] = $content;
        return $this->renderViewOnly($layout, $data);
    }

    public function redirect(string $url, array $data = [], int $status = 200): string
    {
        header('Location: ' . $url, true, 302);
        return '';
    }

    private function renderViewOnly(string $view, array $data): string
    {
        $file = $this->templateDir . '/' . ltrim($view, '/') . '.php';

        if (!file_exists($file)) {
            throw new RuntimeException("Fichier de vue introuvable : {$file}");
        }

        extract($data, EXTR_SKIP);

        $e = fn(?string $val) => ViewRenderer::e($val);

        ob_start();
        try {
            require $file;
            return (string)ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
