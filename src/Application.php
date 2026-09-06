<?php

declare(strict_types=1);

namespace App;

use App\View\ViewRenderer;
use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;

class Application
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly ContainerInterface $container,
        private readonly ViewRenderer $view
    ) {
    }

    public function run(): void
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Supprimer la query string (?...) avant le dispatch
        if (false !== ($pos = strpos($uri, '?'))) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                http_response_code(404);
                echo $this->view->render('error/404', ['title' => 'Page introuvable']);
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                http_response_code(405);
                header('Allow: ' . implode(', ', $allowedMethods));
                echo $this->view->render('error/405', [
                    'title'          => 'Méthode non autorisée',
                    'allowedMethods' => $allowedMethods,
                ]);
                break;

            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                [$controllerClass, $method] = $handler;

                // Le conteneur résout le contrôleur avec toutes ses dépendances
                $controller = $this->container->get($controllerClass);

                // Invocation de la méthode en lui injectant les variables d'URL (ex: id)
                $response = $controller->$method(...array_values($vars));

                if (is_string($response)) {
                    echo $response;
                }
                break;
        }
    }
}
