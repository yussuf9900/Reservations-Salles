<?php

declare(strict_types=1);

namespace App\View;

use App\Model\User;
use RuntimeException;

class ViewRenderer
{
    private string $templateDir;

    public function __construct(?string $templateDir = null)
    {
        $this->templateDir = $templateDir ?? dirname(__DIR__, 2) . '/templates';
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type][] = $message;
    }

    public function getFlashes(): array
    {
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }

    public function render(string $view, array $data = [], string $layout = 'layout/base'): string
    {
        $data['flashes'] = $this->getFlashes();

        if (!isset($data['currentUser']) && !empty($_SESSION['user_id'])) {
            try {
                if (class_exists(User::class)) {
                    $data['currentUser'] = User::find((int)$_SESSION['user_id']);
                }
            } catch (\Throwable) {
                $data['currentUser'] = null;
            }
        }

        if (!isset($data['csrf_token'])) {
            $data['csrf_token'] = $_SESSION['_csrf_token'] ?? '';
        }

        $content = $this->renderViewOnly($view, $data);

        if ($layout === '') {
            return $content;
        }

        $data['content'] = $content;
        return $this->renderViewOnly($layout, $data);
    }

    private function renderViewOnly(string $view, array $data): string
    {
        $file = $this->templateDir . '/' . ltrim($view, '/') . '.php';

        if (!file_exists($file)) {
            throw new RuntimeException("Fichier de vue introuvable : {$file}");
        }

        extract($data, EXTR_SKIP);

        $e = fn(?string $val) => self::e($val);

        ob_start();
        require $file;
        return (string)ob_get_clean();
    }
}
