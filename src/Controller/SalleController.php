<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use App\Service\AuthService;
use App\Service\CsrfService;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;

class SalleController
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly SalleValidator $validator,
        private readonly ViewRenderer $view,
        private readonly CsrfService $csrf,
        private readonly AuthService $auth
    ) {
    }

    public function index(): string
    {
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 6;

        $criteres = [
            'q'            => isset($_GET['q']) ? trim((string)$_GET['q']) : '',
            'type'         => isset($_GET['type']) ? trim((string)$_GET['type']) : '',
            'capacite_min' => isset($_GET['capacite_min']) && is_numeric($_GET['capacite_min']) ? (int)$_GET['capacite_min'] : '',
            'active'       => isset($_GET['active']) && $_GET['active'] !== '' ? (string)$_GET['active'] : '',
            'dispo_debut'  => isset($_GET['dispo_debut']) ? trim((string)$_GET['dispo_debut']) : '',
            'dispo_fin'    => isset($_GET['dispo_fin']) ? trim((string)$_GET['dispo_fin']) : '',
        ];

        $paginator = $this->salles->search($criteres, $page, $perPage);

        $queryParams = array_filter($criteres, fn($v) => $v !== '' && $v !== null);

        return $this->view->render('salle/index', [
            'title'       => 'Liste des salles',
            'salles'      => $paginator->items(),
            'paginator'   => $paginator,
            'criteres'    => $criteres,
            'queryParams' => $queryParams,
            'baseUrl'     => '/salles',
            'isAdmin'     => $this->auth->isAdmin(),
        ]);
    }

    public function show(int $id): string
    {
        $salle = $this->salles->findById($id);
        if ($salle === null) {
            http_response_code(404);
            return $this->view->render('error/404', ['title' => 'Salle non trouvée']);
        }

        return $this->view->render('salle/show', [
            'title'   => 'Détail de la salle : ' . $salle->nom,
            'salle'   => $salle,
            'isAdmin' => $this->auth->isAdmin(),
        ]);
    }

    public function create(): string
    {
        return $this->view->render('salle/form', [
            'title'      => 'Ajouter une salle',
            'old'        => [],
            'errors'     => [],
            'csrf_token' => $this->csrf->getToken(),
        ]);
    }

    public function store(): string
    {
        $data = $_POST;
        if (!isset($data['active'])) {
            $data['active'] = false;
        }

        $result = $this->validator->validate($data);

        if (!$result->isValid()) {
            return $this->view->render('salle/form', [
                'title'      => 'Ajouter une salle',
                'old'        => $data,
                'errors'     => $result->errors(),
                'csrf_token' => $this->csrf->getToken(),
            ]);
        }

        $dto = CreerSalleDTO::builder()->fromArray($result->validated())->build();

        $salle = new Salle([
            'nom'      => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type'     => $dto->type,
            'active'   => $dto->active,
        ]);

        $this->salles->save($salle);

        $this->view->setFlash('success', "La salle '{$salle->nom}' a été créée avec succès.");
        header('Location: /salles/' . $salle->id);
        if (!defined('PHPUNIT_RUNNING')) {
            exit;
        }
        return '';
    }

    public function edit(int $id): string
    {
        $salle = $this->salles->findById($id);
        if ($salle === null) {
            http_response_code(404);
            return $this->view->render('error/404', ['title' => 'Salle non trouvée']);
        }

        return $this->view->render('salle/form', [
            'title'      => 'Modifier la salle ' . $salle->nom,
            'salle'      => $salle,
            'old'        => [
                'nom'      => $salle->nom,
                'batiment' => $salle->batiment,
                'capacite' => $salle->capacite,
                'type'     => $salle->type,
                'active'   => $salle->active,
            ],
            'errors'     => [],
            'csrf_token' => $this->csrf->getToken(),
        ]);
    }

    public function update(int $id): string
    {
        $salle = $this->salles->findById($id);
        if ($salle === null) {
            http_response_code(404);
            return $this->view->render('error/404', ['title' => 'Salle non trouvée']);
        }

        $data = $_POST;
        if (!isset($data['active'])) {
            $data['active'] = false;
        }

        $result = $this->validator->validate($data);

        if (!$result->isValid()) {
            return $this->view->render('salle/form', [
                'title'      => 'Modifier la salle ' . $salle->nom,
                'salle'      => $salle,
                'old'        => $data,
                'errors'     => $result->errors(),
                'csrf_token' => $this->csrf->getToken(),
            ]);
        }

        $dto = CreerSalleDTO::builder()->fromArray($result->validated())->build();

        $salle->nom = $dto->nom;
        $salle->batiment = $dto->batiment;
        $salle->capacite = $dto->capacite;
        $salle->type = $dto->type;
        $salle->active = $dto->active;

        $this->salles->save($salle);

        $this->view->setFlash('success', "La salle '{$salle->nom}' a été mise à jour.");
        header('Location: /salles/' . $salle->id);
        if (!defined('PHPUNIT_RUNNING')) {
            exit;
        }
        return '';
    }
}
