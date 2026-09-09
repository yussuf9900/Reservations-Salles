<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthService;
use App\Service\StatistiquesService;
use App\View\ViewRenderer;

class DashboardController
{
    public function __construct(
        private readonly StatistiquesService $statistiques,
        private readonly AuthService $auth,
        private readonly ViewRenderer $view
    ) {
    }

    public function index(): string
    {
        $stats = $this->statistiques->getStatistiques();

        return $this->view->render('dashboard/index', [
            'title'       => 'Tableau de bord',
            'stats'       => $stats,
            'currentUser' => $this->auth->user(),
        ]);
    }
}
