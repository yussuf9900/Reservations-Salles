<?php

declare(strict_types=1);

use App\Controller\Api\ApiDashboardController;
use App\Controller\Api\ApiReservationController;
use App\Controller\Api\ApiSalleController;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use FastRoute\RouteCollector;

return function (RouteCollector $r): void {
    $r->addRoute('GET', '/', [SalleController::class, 'index']);

    $r->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
    $r->addRoute('POST', '/login', [AuthController::class, 'login']);
    $r->addRoute('POST', '/login/quick', [AuthController::class, 'quickLogin']);
    $r->addRoute('GET', '/logout', [AuthController::class, 'logout']);
    $r->addRoute('POST', '/logout', [AuthController::class, 'logout']);

    $r->addRoute('GET', '/dashboard', [DashboardController::class, 'index']);

    $r->addRoute('GET', '/salles', [SalleController::class, 'index']);
    $r->addRoute('GET', '/salles/create', [SalleController::class, 'create']);
    $r->addRoute('POST', '/salles', [SalleController::class, 'store']);
    $r->addRoute('GET', '/salles/{id:\d+}', [SalleController::class, 'show']);
    $r->addRoute('GET', '/salles/{id:\d+}/edit', [SalleController::class, 'edit']);
    $r->addRoute('POST', '/salles/{id:\d+}/edit', [SalleController::class, 'update']);

    $r->addRoute('GET', '/reservations', [ReservationController::class, 'index']);
    $r->addRoute('GET', '/reservations/create', [ReservationController::class, 'create']);
    $r->addRoute('POST', '/reservations', [ReservationController::class, 'store']);
    $r->addRoute('GET', '/reservations/{id:\d+}', [ReservationController::class, 'show']);
    $r->addRoute('POST', '/reservations/{id:\d+}/cancel', [ReservationController::class, 'cancel']);

    $r->addRoute('GET', '/api/salles', [ApiSalleController::class, 'index']);
    $r->addRoute('GET', '/api/salles/{id:\d+}', [ApiSalleController::class, 'show']);
    $r->addRoute('POST', '/api/salles', [ApiSalleController::class, 'store']);

    $r->addRoute('GET', '/api/reservations', [ApiReservationController::class, 'index']);
    $r->addRoute('GET', '/api/reservations/{id:\d+}', [ApiReservationController::class, 'show']);
    $r->addRoute('POST', '/api/reservations', [ApiReservationController::class, 'store']);
    $r->addRoute('POST', '/api/reservations/{id:\d+}/cancel', [ApiReservationController::class, 'cancel']);

    $r->addRoute('GET', '/api/stats', [ApiDashboardController::class, 'stats']);
};
