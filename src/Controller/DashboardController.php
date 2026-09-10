<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthService;
use App\Service\StatistiquesService;
use App\View\ViewRenderer;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly StatistiquesService $statistiques,
        private readonly AuthService $auth,
        ViewRenderer $view
    ) {
        parent::__construct($view);
    }

    public function index(): string
    {
        $stats = $this->statistiques->getStatistiques();

        return $this->render('dashboard/index', [
            'title'       => 'Tableau de bord',
            'stats'       => $stats,
            'currentUser' => $this->auth->user(),
        ]);
    }
}
