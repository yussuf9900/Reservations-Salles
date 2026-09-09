<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Http\JsonResponse;
use App\Service\StatistiquesService;

class ApiDashboardController
{
    public function __construct(
        private readonly StatistiquesService $statistiques
    ) {
    }

    public function stats(): string
    {
        return JsonResponse::ok($this->statistiques->getStatistiques());
    }
}
