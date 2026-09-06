<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;

class EloquentSalleRepository implements SalleRepositoryInterface
{
    /**
     * @return array<Salle>
     */
    public function all(): array
    {
        return Salle::orderBy('nom', 'asc')->get()->all();
    }

    public function findById(int $id): ?Salle
    {
        return Salle::find($id);
    }

    public function save(Salle $salle): bool
    {
        return $salle->save();
    }

    public function setActif(int $id, bool $active): bool
    {
        $salle = $this->findById($id);
        if (!$salle) {
            return false;
        }

        $salle->active = $active;
        return $salle->save();
    }
}
