<?php

declare(strict_types=1);

namespace App\Http;

class JsonResponse
{
    public static function create(mixed $data, int $status = 200, array $headers = []): string
    {
        http_response_code($status);

        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
            foreach ($headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public static function ok(mixed $data = ['success' => true]): string
    {
        return self::create($data, 200);
    }

    public static function created(mixed $data): string
    {
        return self::create($data, 201);
    }

    public static function error(string $message, int $status = 400, array $details = []): string
    {
        $payload = [
            'error'   => true,
            'message' => $message,
        ];
        if (!empty($details)) {
            $payload['details'] = $details;
        }

        return self::create($payload, $status);
    }

    public static function notFound(string $message = 'Ressource introuvable'): string
    {
        return self::error($message, 404);
    }

    public static function validationError(array $errors): string
    {
        return self::create([
            'error'   => true,
            'message' => 'Les données fournies sont invalides.',
            'errors'  => $errors,
        ], 422);
    }
}
