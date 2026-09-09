<?php

declare(strict_types=1);

namespace App;

use App\Http\JsonResponse;
use App\Middleware\MiddlewareInterface;
use App\View\ViewRenderer;
use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;
use Throwable;

class Application
{
    private array $middlewares = [];

    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly ContainerInterface $container,
        private readonly ViewRenderer $view,
        array $middlewares = []
    ) {
        $this->middlewares = $middlewares;
    }

    public function addMiddleware(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function handle(string $httpMethod, string $uri): mixed
    {
        try {
            $request = [
                'method' => $httpMethod,
                'uri'    => $uri,
            ];

            $pipeline = array_reduce(
                array_reverse($this->middlewares),
                function (callable $next, MiddlewareInterface $middleware) {
                    return function (array $req) use ($next, $middleware) {
                        return $middleware->handle($req, $next);
                    };
                },
                function (array $req) {
                    return $this->dispatchRoute($req['method'], $req['uri']);
                }
            );

            return $pipeline($request);
        } catch (Throwable $e) {
            http_response_code(500);

            if (str_starts_with($uri, '/api/')) {
                return JsonResponse::error($e->getMessage(), 500);
            }

            return $this->view->render('error/500', [
                'title'   => 'Erreur 500',
                'message' => $e->getMessage() . "\n" . $e->getTraceAsString(),
            ]);
        }
    }

    public function run(): void
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        if (false !== ($pos = strpos($uri, '?'))) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $response = $this->handle($httpMethod, $uri);
        if (is_string($response)) {
            echo $response;
        }
    }

    private function dispatchRoute(string $httpMethod, string $uri): mixed
    {
        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                http_response_code(404);
                if (str_starts_with($uri, '/api/')) {
                    return JsonResponse::notFound();
                }
                return $this->view->render('error/404', ['title' => 'Page introuvable']);

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                http_response_code(405);
                header('Allow: ' . implode(', ', $allowedMethods));

                if (str_starts_with($uri, '/api/')) {
                    return JsonResponse::error('Méthode non autorisée', 405, ['allowed' => $allowedMethods]);
                }

                return $this->view->render('error/405', [
                    'title'          => 'Méthode non autorisée',
                    'allowedMethods' => $allowedMethods,
                ]);

            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                [$controllerClass, $method] = $handler;
                $controller = $this->container->get($controllerClass);

                $args = array_map(function ($val) {
                    if (is_string($val) && ctype_digit($val)) {
                        return (int)$val;
                    }
                    return $val;
                }, array_values($vars));

                return $controller->$method(...$args);

            default:
                http_response_code(404);
                return 'Not Found';
        }
    }
}
