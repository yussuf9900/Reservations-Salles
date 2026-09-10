<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use DateTimeInterface;

class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function all(): array
    {
        return Reservation::with('salle')->orderBy('date_debut', 'desc')->get()->all();
    }

    public function findBySalle(int $salleId): array
    {
        return Reservation::with('salle')
            ->where('salle_id', $salleId)
            ->orderBy('date_debut', 'desc')
            ->get()
            ->all();
    }

    public function findById(int $id): ?Reservation
    {
        return Reservation::with('salle')->find($id);
    }

    public function save(Reservation $reservation): bool
    {
        return $reservation->save();
    }

    public function annuler(int $id): bool
    {
        $reservation = $this->findById($id);
        if (!$reservation) {
            return false;
        }

        $reservation->statut = 'annulée';
        return $reservation->save();
    }

    public function trouverConflit(
        int $salleId,
        DateTimeInterface $debut,
        DateTimeInterface $fin,
        ?int $exclureId = null
    ): ?Reservation {
        $query = Reservation::where('salle_id', $salleId)
            ->where('statut', 'confirmée')
            ->where('date_debut', '<', $fin->format('Y-m-d H:i:s'))
            ->where('date_fin', '>', $debut->format('Y-m-d H:i:s'));

        if ($exclureId !== null) {
            $query->where('id', '!=', $exclureId);
        }

        return $query->first();
    }

    public function search(array $criteres = [], int $page = 1, int $perPage = 8): LengthAwarePaginator
    {
        $query = Reservation::with('salle');

        if (!empty($criteres['salle_id']) && is_numeric($criteres['salle_id'])) {
            $query->where('salle_id', (int)$criteres['salle_id']);
        }

        if (!empty($criteres['statut'])) {
            $query->where('statut', $criteres['statut']);
        }

        if (!empty($criteres['q'])) {
            $q = '%' . trim((string)$criteres['q']) . '%';
            $query->where(function ($sub) use ($q) {
                $sub->where('responsable', 'like', $q)
                    ->orWhere('email', 'like', $q)
                    ->orWhere('motif', 'like', $q);
            });
        }

        if (!empty($criteres['date'])) {
            $query->whereDate('date_debut', $criteres['date']);
        }

        return $query->orderBy('date_debut', 'desc')->orderBy('id')->paginate(max(1, $perPage), ['*'], 'page', max(1, $page))->withPath('/reservations');
    }
}
