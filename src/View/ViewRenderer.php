<?php

declare(strict_types=1);

namespace App\View;

use App\Model\User;
use App\Service\CsrfService;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use RuntimeException;

class ViewRenderer
{
    private string $templateDir;

    public function __construct(
        private readonly ?CsrfService $csrf = null,
        ?string $templateDir = null
    ) {
        $this->templateDir = $templateDir ?? dirname(__DIR__, 2) . '/templates';
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function getRequestedFormat(): string
    {
        if (isset($_GET['format'])) {
            $format = strtolower(trim((string)$_GET['format']));
            if (in_array($format, ['json', 'html'], true)) {
                return $format;
            }
        }

        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json') && !str_contains($_SERVER['HTTP_ACCEPT'], 'text/html')) {
            return 'json';
        }

        $envFormat = strtolower(trim((string)($_ENV['APP_RESPONSE_FORMAT'] ?? getenv('APP_RESPONSE_FORMAT') ?: 'html')));
        if ($envFormat === 'json') {
            return 'json';
        }

        return 'html';
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
        $format = $this->getRequestedFormat();

        if ($format === 'json') {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
            return (string)json_encode(
                $this->normalizeDataForJson($view, $data),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }

        $data['currentFormat'] = 'html';
        $queryParams = $_GET ?? [];
        $data['urlFormatHtml'] = '?' . http_build_query(array_merge($queryParams, ['format' => 'html']));
        $data['urlFormatJson'] = '?' . http_build_query(array_merge($queryParams, ['format' => 'json']));

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

        if (empty($data['csrf_token'])) {
            $data['csrf_token'] = $this->csrf?->getToken() ?? ($_SESSION['_csrf_token'] ?? '');
        }

        $content = $this->renderViewOnly($view, $data);

        if ($layout === '') {
            return $content;
        }

        $data['content'] = $content;
        return $this->renderViewOnly($layout, $data);
    }

    private function normalizeDataForJson(string $view, array $data): array
    {
        $excludedKeys = ['flashes', 'csrf_token', 'currentUser', 'content', 'isAdmin', 'salleIdSelectionnee', 'queryParams', 'baseUrl'];
        $clean = [];

        foreach ($data as $key => $val) {
            if (in_array($key, $excludedKeys, true)) {
                continue;
            }
            $clean[$key] = $this->transformValue($val);
        }

        return [
            'success' => true,
            'format'  => 'json',
            'view'    => $view,
            'data'    => $clean,
        ];
    }

    private function transformValue(mixed $value): mixed
    {
        if (is_object($value)) {
            if ($value instanceof Arrayable || method_exists($value, 'toArray')) {
                return $this->transformValue($value->toArray());
            }

            if ($value instanceof DateTimeInterface) {
                return $value->format('c');
            }

            if ($value instanceof \JsonSerializable) {
                return $this->transformValue($value->jsonSerialize());
            }

            return get_object_vars($value);
        }

        if (is_array($value)) {
            $transformed = [];
            foreach ($value as $k => $v) {
                $transformed[$k] = $this->transformValue($v);
            }
            return $transformed;
        }

        return $value;
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
