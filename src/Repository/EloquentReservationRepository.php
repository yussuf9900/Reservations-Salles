<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeInterface;

class EloquentReservationRepository implements ReservationRepositoryInterface
{
    /**
     * @return array<Reservation>
     */
    public function all(): array
    {
        return Reservation::with('salle')->orderBy('date_debut', 'desc')->get()->all();
    }

    /**
     * @return array<Reservation>
     */
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

    /**
     * Deux réservations se chevauchent lorsque :
     * nouveauDebut < reservationExistante.dateFin ET nouvelleFin > reservationExistante.dateDebut
     * Les réservations annulées ne bloquent plus la salle.
     */
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
}
