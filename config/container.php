<?php

declare(strict_types=1);

use App\Application;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\LoggingMiddleware;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\EloquentUserRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\AuthService;
use App\Service\CreerReservationService;
use App\Service\CsrfService;
use App\Service\LoggerService;
use App\Service\StatistiquesService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Container\ContainerInterface;
use function DI\autowire;
use function DI\factory;

return [
    \App\Service\TransactionStrategyInterface::class => autowire(\App\Repository\EloquentTransactionStrategy::class),
    Capsule::class => factory(function (): Capsule {
        $initDb = require __DIR__ . '/database.php';
        return $initDb();
    }),

    SalleRepositoryInterface::class => factory(function (Capsule $capsule): SalleRepositoryInterface {
        return new EloquentSalleRepository();
    }),

    ReservationRepositoryInterface::class => factory(function (Capsule $capsule): ReservationRepositoryInterface {
        return new EloquentReservationRepository();
    }),

    UserRepositoryInterface::class => factory(function (Capsule $capsule): UserRepositoryInterface {
        return new EloquentUserRepository();
    }),

    SalleValidator::class => autowire(SalleValidator::class),
    ReservationValidator::class => autowire(ReservationValidator::class),

    LoggerService::class => autowire(LoggerService::class),
    CsrfService::class => autowire(CsrfService::class),
    AuthService::class => autowire(AuthService::class),
    StatistiquesService::class => autowire(StatistiquesService::class),

    CreerReservationService::class => autowire(CreerReservationService::class),
    AnnulerReservationService::class => autowire(AnnulerReservationService::class),

    \App\Session\SessionManagerInterface::class => autowire(\App\Session\SessionManager::class),
    \App\Http\Strategy\ResponseStrategyInterface::class => factory(function (ContainerInterface $c): \App\Http\Strategy\ResponseStrategyInterface {
        $format = strtolower(trim((string)($_ENV['APP_RESPONSE_FORMAT'] ?? 'html')));
        $strategies = [
            'html' => \App\Http\Strategy\HtmlResponseStrategy::class,
            'json' => \App\Http\Strategy\JsonResponseStrategy::class,
        ];
        if (!isset($strategies[$format])) {
            throw new \InvalidArgumentException('APP_RESPONSE_FORMAT doit être html ou json.');
        }
        return $c->get($strategies[$format]);
    }),
    ViewRenderer::class => autowire(),

    LoggingMiddleware::class => autowire(LoggingMiddleware::class),
    CsrfMiddleware::class => autowire(CsrfMiddleware::class),
    AuthMiddleware::class => autowire(AuthMiddleware::class),

    SalleController::class => autowire(SalleController::class),
    ReservationController::class => autowire(ReservationController::class),
    AuthController::class => autowire(AuthController::class),
    DashboardController::class => autowire(DashboardController::class),

    Dispatcher::class => factory(function (): Dispatcher {
        $routesCallback = require dirname(__DIR__) . '/routes/web.php';
        return \FastRoute\simpleDispatcher(function (RouteCollector $r) use ($routesCallback): void {
            $routesCallback($r);
        });
    }),

    Application::class => factory(function (ContainerInterface $c): Application {
        $middlewares = [
            $c->get(LoggingMiddleware::class),
            $c->get(CsrfMiddleware::class),
            $c->get(AuthMiddleware::class),
        ];

        return new Application(
            $c->get(Dispatcher::class),
            $c,
            $c->get(ViewRenderer::class),
            $middlewares
        );
    }),
];
