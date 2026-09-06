<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;
use InvalidArgumentException;

class CreerReservationDTO
{
    public function __construct(
        public readonly int $salleId,
        public readonly string $responsable,
        public readonly string $email,
        public readonly string $motif,
        public readonly DateTimeImmutable $dateDebut,
        public readonly DateTimeImmutable $dateFin
    ) {
    }

    public static function fromArray(array $data): self
    {
        $debutRaw = $data['date_debut'] ?? $data['dateDebut'] ?? '';
        $finRaw = $data['date_fin'] ?? $data['dateFin'] ?? '';

        try {
            $debut = new DateTimeImmutable((string)$debutRaw);
            $fin = new DateTimeImmutable((string)$finRaw);
        } catch (\Throwable $e) {
            throw new InvalidArgumentException("Format de date invalide pour la réservation : " . $e->getMessage(), 0, $e);
        }

        return new self(
            salleId: (int)($data['salle_id'] ?? $data['salleId'] ?? 0),
            responsable: trim((string)($data['responsable'] ?? '')),
            email: trim((string)($data['email'] ?? '')),
            motif: trim((string)($data['motif'] ?? '')),
            dateDebut: $debut,
            dateFin: $fin
        );
    }
}
