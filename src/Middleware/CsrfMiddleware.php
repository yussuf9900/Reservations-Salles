<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\CsrfService;
use App\View\ViewRenderer;

class CsrfMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly CsrfService $csrf,
        private readonly ViewRenderer $view
    ) {
    }

    public function handle(array $request, callable $next): mixed
    {
        $method = strtoupper($request['method'] ?? 'GET');
        $uri = $request['uri'] ?? '/';

        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

            if (!$this->csrf->validateToken(is_string($token) ? $token : null)) {
                http_response_code(403);
                return $this->view->render('error/403', [
                    'title'          => 'Session expirée ou requête invalide (CSRF)',
                    'message'        => 'Le jeton de sécurité est absent ou invalide. Veuillez rafraîchir la page et réessayer.',
                    'allowedMethods' => ['Jeton CSRF absent ou invalide.'],
                ]);
            }
        }

        return $next($request);
    }
}
