<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;

interface SalleRepositoryInterface
{
    /**
     * @return array<Salle>
     */
    public function all(): array;

    public function findById(int $id): ?Salle;

    public function save(Salle $salle): bool;

    public function setActif(int $id, bool $active): bool;
}
