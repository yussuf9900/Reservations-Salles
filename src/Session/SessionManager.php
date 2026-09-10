<?php

declare(strict_types=1);

namespace App\Session;

use RuntimeException;

final class SessionManager implements SessionManagerInterface
{
    public function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        if (headers_sent()) {
            throw new RuntimeException('Impossible de démarrer la session après les en-têtes.');
        }
        $started = session_start([
            'use_strict_mode' => 1,
            'use_only_cookies' => 1,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        ]);
        if (!$started) {
            throw new RuntimeException('Impossible de démarrer la session.');
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function remove(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    public function regenerate(): void
    {
        $this->start();
        session_regenerate_id(true);
    }

    public function destroy(): void
    {
        $this->start();
        $_SESSION = [];
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'],
        ]);
        session_destroy();
        session_id('');
    }

    public function flash(string $type, string $message): void
    {
        $flashes = $this->get('flash', []);
        $flashes[$type][] = $message;
        $this->set('flash', $flashes);
    }

    public function flashes(): array
    {
        $flashes = $this->get('flash', []);
        $this->remove('flash');
        return $flashes;
    }
}
