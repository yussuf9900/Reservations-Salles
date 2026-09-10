<?php

declare(strict_types=1);

namespace App\Http\Strategy;

interface ResponseStrategyInterface
{
    public function render(string $view, array $data = [], string $layout = 'layout/base'): string;
    public function redirect(string $url, array $data = [], int $status = 200): string;
}
