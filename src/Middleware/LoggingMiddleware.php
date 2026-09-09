<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\LoggerService;

class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly LoggerService $logger
    ) {
    }

    public function handle(array $request, callable $next): mixed
    {
        $startTime = microtime(true);
        $method = $request['method'] ?? 'GET';
        $uri = $request['uri'] ?? '/';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        $response = $next($request);

        $durationMs = round((microtime(true) - $startTime) * 1000, 2);
        $statusCode = http_response_code() ?: 200;

        $this->logger->info(sprintf('%s %s [%d] - %sms', $method, $uri, $statusCode, $durationMs), [
            'ip'       => $ip,
            'status'   => $statusCode,
            'duration' => $durationMs,
        ]);

        return $response;
    }
}
