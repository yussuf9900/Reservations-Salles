<?php

declare(strict_types=1);

use App\Application;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Illuminate\Database\Capsule\Manager as Capsule;
use function DI\autowire;
use function DI\factory;

return [
    // Configuration et démarrage de la base de données Eloquent (Capsule)
    Capsule::class => factory(function (): Capsule {
        $initDb = require __DIR__ . '/database.php';
        return $initDb();
    }),

    // Abstractions des données (Interfaces liées aux implémentations Eloquent)
    SalleRepositoryInterface::class => factory(function (Capsule $capsule): SalleRepositoryInterface {
        // Garantit que Eloquent est initialisé avant d'utiliser le repository
        return new EloquentSalleRepository();
    }),

    ReservationRepositoryInterface::class => factory(function (Capsule $capsule): ReservationRepositoryInterface {
        return new EloquentReservationRepository();
    }),

    // Validateurs
    SalleValidator::class => autowire(SalleValidator::class),
    ReservationValidator::class => autowire(ReservationValidator::class),

    // Services métier (injection explicite par constructeur)
    CreerReservationService::class => autowire(CreerReservationService::class),
    AnnulerReservationService::class => autowire(AnnulerReservationService::class),

    // Moteur de rendu
    ViewRenderer::class => autowire(ViewRenderer::class),

    // Contrôleurs HTTP
    SalleController::class => autowire(SalleController::class),
    ReservationController::class => autowire(ReservationController::class),

    // Routeur FastRoute Dispatcher
    Dispatcher::class => factory(function (): Dispatcher {
        $routesCallback = require dirname(__DIR__) . '/routes/web.php';
        return \FastRoute\simpleDispatcher(function (RouteCollector $r) use ($routesCallback): void {
            $routesCallback($r);
        });
    }),

    // Application principale
    Application::class => autowire(Application::class),
];
