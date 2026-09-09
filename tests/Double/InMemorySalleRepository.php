<?php

declare(strict_types=1);

namespace Tests\Double;

use App\Model\Salle;
use App\Pagination\Paginator;
use App\Repository\SalleRepositoryInterface;

class InMemorySalleRepository implements SalleRepositoryInterface
{
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

    public function search(array $criteres = [], int $page = 1, int $perPage = 6): Paginator
    {
        $filtered = array_values(array_filter($this->salles, function (Salle $salle) use ($criteres) {
            if (!empty($criteres['q'])) {
                $q = strtolower(trim((string)$criteres['q']));
                $nomMatch = str_contains(strtolower($salle->nom), $q);
                $batMatch = str_contains(strtolower($salle->batiment), $q);
                if (!$nomMatch && !$batMatch) {
                    return false;
                }
            }

            if (!empty($criteres['type']) && $salle->type !== $criteres['type']) {
                return false;
            }

            if (!empty($criteres['capacite_min']) && $salle->capacite < (int)$criteres['capacite_min']) {
                return false;
            }

            if (isset($criteres['active']) && $criteres['active'] !== '') {
                if ($salle->active !== (bool)$criteres['active']) {
                    return false;
                }
            }

            return true;
        }));

        $total = count($filtered);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($filtered, $offset, $perPage);

        return new Paginator($items, $total, $perPage, $page);
    }
}
