<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Repository\ReservationRepositoryInterface;
use Illuminate\Database\Capsule\Manager as Capsule;
use PDO;
use Throwable;

final class AnnulerReservationService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
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

        try {
            if (class_exists(Capsule::class)) {
                $capsule = Capsule::getInstance();
                if ($capsule !== null) {
                    $connection = $capsule->getConnection();
                    if ($connection->getPdo() instanceof PDO) {
                        return $connection->transaction($action);
                    }
                }
            }
        } catch (ReservationIntrouvableException $e) {
            throw $e;
        } catch (Throwable) {
        }

        return $action();
    }
}
