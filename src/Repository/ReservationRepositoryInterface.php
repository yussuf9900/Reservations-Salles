<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeInterface;

interface ReservationRepositoryInterface
{
    /**
     * @return array<Reservation>
     */
    public function all(): array;

    /**
     * @return array<Reservation>
     */
    public function findBySalle(int $salleId): array;

    public function findById(int $id): ?Reservation;

    public function save(Reservation $reservation): bool;

    public function annuler(int $id): bool;

    /**
     * Recherche un chevauchement avec une réservation confirmée existante :
     * nouveauDebut < existante.date_fin ET nouvelleFin > existante.date_debut
     */
    public function trouverConflit(
        int $salleId,
        DateTimeInterface $debut,
        DateTimeInterface $fin,
        ?int $exclureId = null
    ): ?Reservation;
}
