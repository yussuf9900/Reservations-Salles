<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface SalleRepositoryInterface
{
    public function all(): array;

    public function findById(int $id): ?Salle;

    public function findByIdForUpdate(int $id): ?Salle;

    public function save(Salle $salle): bool;

    public function setActif(int $id, bool $active): bool;

    public function search(array $criteres = [], int $page = 1, int $perPage = 6): LengthAwarePaginator;
}
