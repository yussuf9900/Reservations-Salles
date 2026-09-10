<?php

declare(strict_types=1);

namespace App\Http\Strategy;

use App\Http\JsonResponse;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;

final class JsonResponseStrategy implements ResponseStrategyInterface
{
    public function __construct(private readonly \App\Session\SessionManagerInterface $session)
    {
    }

    public function render(string $view, array $data = [], string $layout = 'layout/base'): string
    {
        $data['flashes'] = $this->session->flashes();
        $status = http_response_code() ?: 200;
        if (!empty($data['errors'])) {
            $status = 422;
        }
        foreach (['currentUser', 'content', 'isAdmin', 'queryParams', 'baseUrl', 'old'] as $key) {
            unset($data[$key]);
        }
        return JsonResponse::create([
            'success' => $status < 400,
            'format' => 'json',
            'view' => $view,
            'data' => $this->transformValue($data),
        ], $status);
    }

    public function redirect(string $url, array $data = [], int $status = 200): string
    {
        http_response_code($status);
        return $this->render('success', $data + ['location' => $url]);
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
}
