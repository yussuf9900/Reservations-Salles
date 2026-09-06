<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Repository\ReservationRepositoryInterface;

final class AnnulerReservationService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations
    ) {
    }

    public function execute(int $reservationId): bool
    {
        $reservation = $this->reservations->findById($reservationId);
        if ($reservation === null) {
            throw new ReservationIntrouvableException("La réservation #{$reservationId} est introuvable.");
        }

        return $this->reservations->annuler($reservationId);
    }
}
