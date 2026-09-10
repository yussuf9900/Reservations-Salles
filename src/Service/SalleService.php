<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

final class SalleService
{
    public function __construct(private readonly SalleRepositoryInterface $salles)
    {
    }

    public function save(CreerSalleDTO $dto, ?Salle $salle = null): Salle
    {
        $salle ??= new Salle();
        $salle->fill([
            'nom' => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type' => $dto->type,
            'active' => $dto->active,
        ]);
        $this->salles->save($salle);
        return $salle;
    }
}
