<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use DateTimeImmutable;

final class CreerReservationService
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationRepositoryInterface $reservations,
        private readonly TransactionStrategyInterface $transactions,
        private readonly ?LoggerService $logger = null
    ) {
    }

    public function execute(CreerReservationDTO $dto): Reservation
    {
        $action = function () use ($dto): Reservation {
            $salle = $this->salles->findByIdForUpdate($dto->salleId);
            if ($salle === null) {
                throw new SalleIndisponibleException("La salle sélectionnée n'existe pas.");
            }

            if (!$salle->active) {
                throw new SalleIndisponibleException("Cette salle ne peut pas être réservée.");
            }

            if ($dto->dateDebut >= $dto->dateFin) {
                throw new SalleIndisponibleException("La date de début doit précéder la date de fin.");
            }

            $dureeSecondes = $dto->dateFin->getTimestamp() - $dto->dateDebut->getTimestamp();
            if ($dureeSecondes > (4 * 3600)) {
                throw new SalleIndisponibleException("Une réservation ne peut pas dépasser quatre heures.");
            }

            $maintenant = new DateTimeImmutable();
            if ($dto->dateDebut <= $maintenant) {
                throw new SalleIndisponibleException("La réservation doit commencer dans le futur.");
            }

            $conflit = $this->reservations->trouverConflit($dto->salleId, $dto->dateDebut, $dto->dateFin);
            if ($conflit !== null) {
                throw new SalleIndisponibleException("La salle est indisponible pendant cette période.");
            }

            $reservation = new Reservation([
                'salle_id'    => $dto->salleId,
                'responsable' => $dto->responsable,
                'email'       => $dto->email,
                'motif'       => $dto->motif,
                'date_debut'  => $dto->dateDebut->format('Y-m-d H:i:s'),
                'date_fin'    => $dto->dateFin->format('Y-m-d H:i:s'),
                'statut'      => 'confirmée',
            ]);

            $this->reservations->save($reservation);

            if ($this->logger !== null) {
                $this->logger->info('Réservation confirmée', [
                    'salle_id'    => $reservation->salle_id,
                    'responsable' => $reservation->responsable,
                    'debut'       => $reservation->date_debut,
                    'fin'         => $reservation->date_fin,
                ]);
            }

            return $reservation;
        };

        return $this->transactions->execute($action);
    }
}
