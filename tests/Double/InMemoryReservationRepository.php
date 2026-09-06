<?php

declare(strict_types=1);

namespace Tests\Double;

use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use DateTimeImmutable;
use DateTimeInterface;

class InMemoryReservationRepository implements ReservationRepositoryInterface
{
    /**
     * @var array<int, Reservation>
     */
    private array $reservations = [];
    private int $nextId = 1;

    public function all(): array
    {
        return array_values($this->reservations);
    }

    public function findBySalle(int $salleId): array
    {
        return array_values(array_filter(
            $this->reservations,
            fn(Reservation $r) => (int)$r->salle_id === $salleId
        ));
    }

    public function findById(int $id): ?Reservation
    {
        return $this->reservations[$id] ?? null;
    }

    public function save(Reservation $reservation): bool
    {
        if (empty($reservation->id)) {
            $reservation->id = $this->nextId++;
        }
        $this->reservations[$reservation->id] = $reservation;
        return true;
    }

    public function annuler(int $id): bool
    {
        if (!isset($this->reservations[$id])) {
            return false;
        }
        $this->reservations[$id]->statut = 'annulée';
        return true;
    }

    public function trouverConflit(
        int $salleId,
        DateTimeInterface $debut,
        DateTimeInterface $fin,
        ?int $exclureId = null
    ): ?Reservation {
        $debutTs = $debut->getTimestamp();
        $finTs = $fin->getTimestamp();

        foreach ($this->reservations as $res) {
            if ((int)$res->salle_id !== $salleId) {
                continue;
            }
            if ($res->statut !== 'confirmée') {
                continue;
            }
            if ($exclureId !== null && (int)$res->id === $exclureId) {
                continue;
            }

            $resDebut = $res->date_debut instanceof DateTimeInterface
                ? $res->date_debut->getTimestamp()
                : (new DateTimeImmutable((string)$res->date_debut))->getTimestamp();

            $resFin = $res->date_fin instanceof DateTimeInterface
                ? $res->date_fin->getTimestamp()
                : (new DateTimeImmutable((string)$res->date_fin))->getTimestamp();

            // Chevauchement : nouveauDebut < reservationExistante.dateFin ET nouvelleFin > reservationExistante.dateDebut
            if ($debutTs < $resFin && $finTs > $resDebut) {
                return $res;
            }
        }

        return null;
    }
}
