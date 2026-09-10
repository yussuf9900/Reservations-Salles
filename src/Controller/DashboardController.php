<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AuthService;
use App\Service\CsrfService;
use App\Service\StatistiquesService;
use App\View\ViewRenderer;

class DashboardController extends AbstractController
{
    public function __construct(
        ViewRenderer $view,
        AuthService $auth,
        CsrfService $csrf,
        private readonly StatistiquesService $statistiques
    ) {
        parent::__construct($view, $auth, $csrf);
    }

    public function index(): string
    {
        $stats = $this->statistiques->getStatistiques();

        return $this->render('dashboard/index', [
            'title'       => 'Tableau de bord',
            'stats'       => $stats,
            'currentUser' => $this->user(),
        ]);
    }
}
