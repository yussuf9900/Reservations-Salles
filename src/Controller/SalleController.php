<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use App\Validation\SalleValidator;
use App\View\ViewRenderer;

class SalleController
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly SalleValidator $validator,
        private readonly ViewRenderer $view
    ) {
    }

    public function index(): string
    {
        $listeSalles = $this->salles->all();

        return $this->view->render('salle/index', [
            'title'  => 'Liste des salles',
            'salles' => $listeSalles,
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
            'title' => 'Détail de la salle : ' . $salle->nom,
            'salle' => $salle,
        ]);
    }

    public function create(): string
    {
        return $this->view->render('salle/form', [
            'title'  => 'Ajouter une salle',
            'old'    => [],
            'errors' => [],
        ]);
    }

    public function store(): string
    {
        $data = $_POST;

        $result = $this->validator->validate($data);

        if (!$result->isValid()) {
            return $this->view->render('salle/form', [
                'title'  => 'Ajouter une salle',
                'old'    => $data,
                'errors' => $result->errors(),
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
        exit;
    }

    public function edit(int $id): string
    {
        $salle = $this->salles->findById($id);
        if ($salle === null) {
            http_response_code(404);
            return $this->view->render('error/404', ['title' => 'Salle non trouvée']);
        }

        return $this->view->render('salle/form', [
            'title'  => 'Modifier la salle ' . $salle->nom,
            'salle'  => $salle,
            'old'    => [
                'nom'      => $salle->nom,
                'batiment' => $salle->batiment,
                'capacite' => $salle->capacite,
                'type'     => $salle->type,
                'active'   => $salle->active,
            ],
            'errors' => [],
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
        $result = $this->validator->validate($data);

        if (!$result->isValid()) {
            return $this->view->render('salle/form', [
                'title'  => 'Modifier la salle ' . $salle->nom,
                'salle'  => $salle,
                'old'    => $data,
                'errors' => $result->errors(),
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
        exit;
    }
}
