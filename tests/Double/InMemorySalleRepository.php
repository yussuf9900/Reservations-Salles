<?php

declare(strict_types=1);

namespace Tests\Double;

use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

class InMemorySalleRepository implements SalleRepositoryInterface
{
    /**
     * @var array<int, Salle>
     */
    private array $salles = [];
    private int $nextId = 1;

    public function all(): array
    {
        return array_values($this->salles);
    }

    public function findById(int $id): ?Salle
    {
        return $this->salles[$id] ?? null;
    }

    public function save(Salle $salle): bool
    {
        if (empty($salle->id)) {
            $salle->id = $this->nextId++;
        }
        $this->salles[$salle->id] = $salle;
        return true;
    }

    public function setActif(int $id, bool $active): bool
    {
        if (!isset($this->salles[$id])) {
            return false;
        }
        $this->salles[$id]->active = $active;
        return true;
    }
}
