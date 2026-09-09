<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;
use App\Pagination\Paginator;

class EloquentSalleRepository implements SalleRepositoryInterface
{
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

    public function search(array $criteres = [], int $page = 1, int $perPage = 6): Paginator
    {
        $query = Salle::query();

        if (!empty($criteres['q'])) {
            $q = '%' . trim((string)$criteres['q']) . '%';
            $query->where(function ($sub) use ($q) {
                $sub->where('nom', 'like', $q)
                    ->orWhere('batiment', 'like', $q);
            });
        }

        if (!empty($criteres['type'])) {
            $query->where('type', $criteres['type']);
        }

        if (!empty($criteres['capacite_min']) && is_numeric($criteres['capacite_min'])) {
            $query->where('capacite', '>=', (int)$criteres['capacite_min']);
        }

        if (isset($criteres['active']) && $criteres['active'] !== '') {
            $query->where('active', (bool)$criteres['active']);
        }

        if (!empty($criteres['dispo_debut']) && !empty($criteres['dispo_fin'])) {
            $debut = $criteres['dispo_debut'];
            $fin = $criteres['dispo_fin'];
            $query->whereDoesntHave('reservations', function ($sub) use ($debut, $fin) {
                $sub->where('statut', 'confirmée')
                    ->where('date_debut', '<', $fin)
                    ->where('date_fin', '>', $debut);
            });
        }

        $total = $query->count();
        $items = $query->orderBy('nom', 'asc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get()
            ->all();

        return new Paginator($items, $total, $perPage, $page);
    }
}
