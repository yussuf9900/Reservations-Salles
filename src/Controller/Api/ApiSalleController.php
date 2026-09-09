<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\CreerSalleDTO;
use App\Http\JsonResponse;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use App\Validation\SalleValidator;

class ApiSalleController
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly SalleValidator $validator
    ) {
    }

    public function index(): string
    {
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) && is_numeric($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 10;

        $criteres = [
            'q'            => $_GET['q'] ?? null,
            'type'         => $_GET['type'] ?? null,
            'capacite_min' => $_GET['capacite_min'] ?? null,
            'active'       => $_GET['active'] ?? null,
        ];

        $paginator = $this->salles->search($criteres, $page, $perPage);
        return JsonResponse::ok($paginator->toArray());
    }

    public function show(int $id): string
    {
        $salle = $this->salles->findById($id);
        if ($salle === null) {
            return JsonResponse::notFound("Salle #{$id} introuvable.");
        }

        return JsonResponse::ok([
            'salle'        => $salle->toArray(),
            'reservations' => $salle->reservations?->toArray() ?? [],
        ]);
    }

    public function store(): string
    {
        $input = file_get_contents('php://input');
        $data = !empty($input) ? json_decode($input, true) : null;
        if (!is_array($data)) {
            $data = $_POST;
        }

        $result = $this->validator->validate($data);
        if (!$result->isValid()) {
            return JsonResponse::validationError($result->errors());
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
        return JsonResponse::created($salle->toArray());
    }
}
