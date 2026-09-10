<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTO;
use App\Repository\SalleRepositoryInterface;
use App\Service\AuthService;
use App\Service\CsrfService;
use App\Service\SalleService;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;

class SalleController extends AbstractController
{
    public function __construct(
        ViewRenderer $view,
        AuthService $auth,
        CsrfService $csrf,
        private readonly SalleRepositoryInterface $salles,
        private readonly SalleValidator $validator,
        private readonly SalleService $service
    ) {
        parent::__construct($view, $auth, $csrf);
    }

    public function index(): string
    {
        $page = $this->getPage();
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
        $paginator->appends($queryParams);

        return $this->render('salle/index', [
            'title'       => 'Liste des salles',
            'salles'      => $paginator->items(),
            'paginator'   => $paginator,
            'criteres'    => $criteres,
            'queryParams' => $queryParams,
            'baseUrl'     => '/salles',
            'isAdmin'     => $this->isAdmin(),
        ]);
    }

    public function show(int $id): string
    {
        $salle = $this->salles->findById($id);
        if ($salle === null) {
            http_response_code(404);
            return $this->render('error/404', ['title' => 'Salle non trouvée']);
        }

        return $this->render('salle/show', [
            'title'   => 'Détail de la salle : ' . $salle->nom,
            'salle'   => $salle,
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    public function create(): string
    {
        return $this->render('salle/form', [
            'title'      => 'Ajouter une salle',
            'old'        => [],
            'errors'     => [],
            'csrf_token' => $this->csrfToken(),
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
            return $this->render('salle/form', [
                'title'      => 'Ajouter une salle',
                'old'        => $data,
                'errors'     => $result->errors(),
                'csrf_token' => $this->csrfToken(),
            ]);
        }

        $dto = CreerSalleDTO::builder()->fromArray($result->validated())->build();

        $salle = $this->service->save($dto);

        $this->flash('success', "La salle '{$salle->nom}' a été créée avec succès.");
        return $this->redirect('/salles/' . $salle->id, ['salle' => $salle], 201);
    }

    public function edit(int $id): string
    {
        $salle = $this->salles->findById($id);
        if ($salle === null) {
            http_response_code(404);
            return $this->render('error/404', ['title' => 'Salle non trouvée']);
        }

        return $this->render('salle/form', [
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
            'csrf_token' => $this->csrfToken(),
        ]);
    }

    public function update(int $id): string
    {
        $salle = $this->salles->findById($id);
        if ($salle === null) {
            http_response_code(404);
            return $this->render('error/404', ['title' => 'Salle non trouvée']);
        }

        $data = $_POST;
        if (!isset($data['active'])) {
            $data['active'] = false;
        }

        $result = $this->validator->validate($data);

        if (!$result->isValid()) {
            return $this->render('salle/form', [
                'title'      => 'Modifier la salle ' . $salle->nom,
                'salle'      => $salle,
                'old'        => $data,
                'errors'     => $result->errors(),
                'csrf_token' => $this->csrfToken(),
            ]);
        }

        $dto = CreerSalleDTO::builder()->fromArray($result->validated())->build();

        $salle = $this->service->save($dto, $salle);

        $this->flash('success', "La salle '{$salle->nom}' a été mise à jour.");
        return $this->redirect('/salles/' . $salle->id, ['salle' => $salle]);
    }
}
