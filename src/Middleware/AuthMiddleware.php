<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\JsonResponse;
use App\Service\AuthService;
use App\View\ViewRenderer;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Routes publiques accessibles sans authentification.
     */
    private const PUBLIC_ROUTES = [
        '#^/login$#',
        '#^/login/quick$#',
        '#^/logout$#',
    ];

    /**
     * Routes réservées exclusivement aux Administrateurs.
     */
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

        // 1. Endpoints de l'API REST (/api/*) exemptés de l'authentification par session web
        if (str_starts_with($uri, '/api/')) {
            return $next($request);
        }

        // 2. Vérifier si la route est publique (/login, /login/quick, /logout)
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

        // 3. Toutes les autres routes web nécessitent une authentification
        if (!$this->auth->check()) {
            if ($this->view->getRequestedFormat() === 'json') {
                http_response_code(401);
                return JsonResponse::error('Authentification requise pour accéder à cette ressource.', 401);
            }

            $this->view->setFlash('error', 'Veuillez vous connecter pour accéder à cet espace.');
            $target = ($uri !== '/' && $uri !== '') ? '?redirect=' . urlencode($uri) : '';
            header('Location: /login' . $target);
            if (!defined('PHPUNIT_RUNNING')) {
                exit;
            }
            return '';
        }

        // 4. Contrôle d'accès basé sur les rôles (RBAC) pour les routes Administrateur
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
            if ($this->view->getRequestedFormat() === 'json') {
                return JsonResponse::error('Accès refusé. Privilèges Administrateur requis.', 403);
            }

            return $this->view->render('error/403', [
                'title'          => 'Accès Refusé',
                'message'        => 'Cette action nécessite les privilèges Administrateur.',
                'allowedMethods' => ['Droits administrateur requis.'],
            ]);
        }

        return $next($request);
    }
}
