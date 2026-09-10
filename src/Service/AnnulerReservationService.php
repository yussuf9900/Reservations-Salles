<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Repository\ReservationRepositoryInterface;

final class AnnulerReservationService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
        private readonly TransactionStrategyInterface $transactions,
        private readonly ?LoggerService $logger = null
    ) {
    }

    public function execute(int $reservationId): bool
    {
        $action = function () use ($reservationId): bool {
            $reservation = $this->reservations->findById($reservationId);
            if ($reservation === null) {
                throw new ReservationIntrouvableException("La réservation #{$reservationId} est introuvable.");
            }

            $result = $this->reservations->annuler($reservationId);

            if ($result && $this->logger !== null) {
                $this->logger->info('Réservation annulée', [
                    'reservation_id' => $reservationId,
                ]);
            }

            return $result;
        };

        return $this->transactions->execute($action);
    }
}
