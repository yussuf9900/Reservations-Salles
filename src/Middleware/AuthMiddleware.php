<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\AuthService;
use App\View\ViewRenderer;

class AuthMiddleware implements MiddlewareInterface
{
    private const PUBLIC_ROUTES = [
        '#^/login$#',
        '#^/login/quick$#',
        '#^/logout$#',
    ];

    private const ADMIN_PATTERNS = [
        '#^/salles/create$#',
        '#^/salles/\d+/edit$#',
    ];

    public function __construct(
        private readonly AuthService $auth,
        private readonly ViewRenderer $view
    ) {
    }

    public function handle(array $request, callable $next): mixed
    {
        $uri = $request['uri'] ?? '/';
        $method = strtoupper($request['method'] ?? 'GET');

        if (str_starts_with($uri, '/api/')) {
            $uri = substr($uri, 4);
        }

        $isPublic = false;
        foreach (self::PUBLIC_ROUTES as $pattern) {
            if (preg_match($pattern, $uri)) {
                $isPublic = true;
                break;
            }
        }

        if ($isPublic) {
            return $next($request);
        }

        if (!$this->auth->check()) {

            $this->view->setFlash('error', 'Veuillez vous connecter pour accéder à cet espace.');
            $target = ($uri !== '/' && $uri !== '') ? '?redirect=' . urlencode($uri) : '';
            http_response_code(401);
            return $this->view->redirect('/login' . $target, ['message' => 'Authentification requise.'], 401);
        }

        $isAdminRoute = false;
        foreach (self::ADMIN_PATTERNS as $pattern) {
            if (preg_match($pattern, $uri)) {
                $isAdminRoute = true;
                break;
            }
        }

        if (!$isAdminRoute && $uri === '/salles' && $method === 'POST') {
            $isAdminRoute = true;
        }

        if ($isAdminRoute && !$this->auth->isAdmin()) {
            http_response_code(403);

            return $this->view->render('error/403', [
                'title'          => 'Accès Refusé',
                'message'        => 'Cette action nécessite les privilèges Administrateur.',
                'allowedMethods' => ['Droits administrateur requis.'],
            ]);
        }

        return $next($request);
    }
}
