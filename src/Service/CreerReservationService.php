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
        private readonly ReservationRepositoryInterface $reservations
    ) {
    }

    public function execute(CreerReservationDTO $dto): Reservation
    {
        // 1. Retrouver la salle
        $salle = $this->salles->findById($dto->salleId);
        if ($salle === null) {
            throw new SalleIndisponibleException("La salle sélectionnée n'existe pas.");
        }

        // 2. Vérifier qu'elle est active
        if (!$salle->active) {
            throw new SalleIndisponibleException("Cette salle ne peut pas être réservée.");
        }

        // 3. Vérifier que le début précède la fin
        if ($dto->dateDebut >= $dto->dateFin) {
            throw new SalleIndisponibleException("La date de début doit précéder la date de fin.");
        }

        // 4. Vérifier que la durée ne dépasse pas 4 heures (14400 secondes)
        $dureeSecondes = $dto->dateFin->getTimestamp() - $dto->dateDebut->getTimestamp();
        if ($dureeSecondes > (4 * 3600)) {
            throw new SalleIndisponibleException("Une réservation ne peut pas dépasser quatre heures.");
        }

        // 5. Vérifier que la date est future
        $maintenant = new DateTimeImmutable();
        if ($dto->dateDebut <= $maintenant) {
            throw new SalleIndisponibleException("La réservation doit commencer dans le futur.");
        }

        // 6. Rechercher les chevauchements
        $conflit = $this->reservations->trouverConflit($dto->salleId, $dto->dateDebut, $dto->dateFin);
        if ($conflit !== null) {
            throw new SalleIndisponibleException("La salle est indisponible pendant cette période.");
        }

        // 7. Créer la réservation
        $reservation = new Reservation([
            'salle_id'    => $dto->salleId,
            'responsable' => $dto->responsable,
            'email'       => $dto->email,
            'motif'       => $dto->motif,
            'date_debut'  => $dto->dateDebut->format('Y-m-d H:i:s'),
            'date_fin'    => $dto->dateFin->format('Y-m-d H:i:s'),
            'statut'      => 'confirmée',
        ]);

        // 8. Enregistrer
        $this->reservations->save($reservation);

        // 9. Retourner le résultat
        return $reservation;
    }
}
