<?php

declare(strict_types=1);

namespace App\Session;

interface SessionManagerInterface
{
    public function start(): void;
    public function get(string $key, mixed $default = null): mixed;
    public function set(string $key, mixed $value): void;
    public function remove(string $key): void;
    public function regenerate(): void;
    public function destroy(): void;
    public function flash(string $type, string $message): void;
    public function flashes(): array;
}
